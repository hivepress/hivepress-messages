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
use HivePress\Menus;
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
	 * Controller name.
	 *
	 * @var string
	 */
	protected static $name;

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

					'view_chats' => [
						'title'    => esc_html__( 'My Messages', 'hivepress-messages' ),
						'path'     => '/account/chats',
						'redirect' => 'redirect_chats_page',
						'action'   => 'render_chats_page',
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

		if ( false === $recipient || $recipient->ID === $sender->ID ) {
			return hp\rest_error( 400 );
		}

		// Get listing.
		if ( $form->get_value( 'listing_id' ) ) {
			$listing = Models\Listing::get( $form->get_value( 'listing_id' ) );

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
					'message_url'  => self::get_url( 'view_chat', [ 'recipient_id' => $recipient->ID ] ),
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
	 * Redirects chats page.
	 *
	 * @return mixed
	 */
	public function redirect_chats_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return add_query_arg( 'redirect', rawurlencode( hp\get_current_url() ), User::get_url( 'login_user' ) );
		}
	}

	/**
	 * Renders chats page.
	 *
	 * @return string
	 */
	public function render_chats_page() {
		$output = 'todo';

		return $output;
	}
}
