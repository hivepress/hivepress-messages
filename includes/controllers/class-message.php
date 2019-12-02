<?php
/**
 * Message controller.
 *
 * @package HivePress\Controllers
 */

namespace HivePress\Controllers;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Forms;
use HivePress\Blocks;
use HivePress\Emails;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message controller class.
 *
 * @class Message
 */
class Message extends Controller {

	/**
	 * Controller routes.
	 *
	 * @var array
	 */
	protected static $routes = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Controller arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					[
						'path'      => '/messages',
						'rest'      => true,

						'endpoints' => [
							[
								'methods' => 'POST',
								'action'  => 'send_message',
							],
						],
					],

					'thread_messages' => [
						'title'    => esc_html__( 'Messages', 'hivepress-messages' ),
						'path'     => '/account/messages',
						'redirect' => 'redirect_messages_thread_page',
						'action'   => 'render_messages_thread_page',
					],

					'view_messages'   => [
						'title'    => esc_html__( 'Messages', 'hivepress-messages' ),
						'path'     => '/account/messages/(?P<user_id>\d+)',
						'redirect' => 'redirect_messages_view_page',
						'action'   => 'render_messages_view_page',
					],
				],
			],
			$args
		);

		parent::init( $args );
	}

	/**
	 * Sends message.
	 *
	 * @param WP_REST_Request $request API request.
	 * @return WP_Rest_Response
	 */
	public function send_message( $request ) {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hp\rest_error( 401 );
		}

		// Validate form.
		$form = new Forms\Message_Send();

		$form->set_values( $request->get_params() );

		if ( ! $form->validate() ) {
			return hp\rest_error( 400, $form->get_errors() );
		}

		// Get sender.
		$sender_id = $request->get_param( 'sender_id' ) ? $request->get_param( 'sender_id' ) : get_current_user_id();
		$sender    = get_userdata( $sender_id );

		if ( false === $sender ) {
			return hp\rest_error( 400 );
		}

		if ( get_current_user_id() !== $sender->ID && ! current_user_can( 'edit_users' ) ) {
			return hp\rest_error( 403 );
		}

		// Get recipient.
		$recipient = get_userdata( $form->get_value( 'recipient_id' ) );

		if ( false === $recipient ) {
			return hp\rest_error( 400 );
		}

		if ( $recipient->ID === $sender->ID ) {
			return hp\rest_error( 403, esc_html__( "You can't send messages to yourself.", 'hivepress-messages' ) );
		}

		// Get listing.
		if ( $form->get_value( 'listing_id' ) ) {
			$listing = Models\Listing::get_by_id( $form->get_value( 'listing_id' ) );

			if ( is_null( $listing ) || $listing->get_status() !== 'publish' ) {
				return hp\rest_error( 400 );
			}
		}

		// Add message.
		$message = new Models\Message();

		$message->fill(
			array_merge(
				$form->get_values(),
				[
					'sender_id'    => $sender->ID,
					'sender_name'  => $sender->display_name,
					'sender_email' => $sender->user_email,
				]
			)
		);

		if ( ! $message->save() ) {
			return hp\rest_error( 400 );
		}

		// Send email.
		( new Emails\Message_Send(
			[
				'recipient' => $recipient->user_email,
				'tokens'    => [
					'user_name'    => $recipient->display_name,
					'message_url'  => self::get_url( 'view_messages', [ 'user_id' => $sender->ID ] ),
					'message_text' => $message->get_text(),
				],
			]
		) )->send();

		return new \WP_Rest_Response(
			[
				'data' => [
					'id' => $message->get_id(),
				],
			],
			200
		);
	}

	/**
	 * Redirects messages thread page.
	 *
	 * @return mixed
	 */
	public function redirect_messages_thread_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return add_query_arg( 'redirect', rawurlencode( hp\get_current_url() ), User::get_url( 'login_user' ) );
		}

		// Check messages.
		$messages = array_merge(
			get_comments(
				[
					'type'    => 'hp_message',
					'user_id' => get_current_user_id(),
					'number'  => 1,
					'fields'  => 'ids',
				]
			),
			get_comments(
				[
					'type'   => 'hp_message',
					'karma'  => get_current_user_id(),
					'number' => 1,
					'fields' => 'ids',
				]
			)
		);

		if ( empty( $messages ) ) {
			return true;
		}
	}

	/**
	 * Renders messages thread page.
	 *
	 * @return string
	 */
	public function render_messages_thread_page() {
		return ( new Blocks\Template( [ 'template' => 'messages_thread_page' ] ) )->render();
	}

	/**
	 * Redirects messages view page.
	 *
	 * @return mixed
	 */
	public function redirect_messages_view_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return add_query_arg( 'redirect', rawurlencode( hp\get_current_url() ), User::get_url( 'login_user' ) );
		}

		// Check user.
		$user = get_userdata( absint( get_query_var( 'hp_user_id' ) ) );

		if ( false === $user || get_current_user_id() === $user->ID ) {
			return true;
		}

		// Check messages.
		$messages = array_merge(
			get_comments(
				[
					'type'    => 'hp_message',
					'user_id' => get_current_user_id(),
					'karma'   => $user->ID,
					'number'  => 1,
					'fields'  => 'ids',
				]
			),
			get_comments(
				[
					'type'    => 'hp_message',
					'user_id' => $user->ID,
					'karma'   => get_current_user_id(),
					'number'  => 1,
					'fields'  => 'ids',
				]
			)
		);

		if ( empty( $messages ) ) {
			return true;
		}
	}

	/**
	 * Renders messages view page.
	 *
	 * @return string
	 */
	public function render_messages_view_page() {
		return ( new Blocks\Template( [ 'template' => 'messages_view_page' ] ) )->render();
	}
}
