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
final class Message extends Controller {

	/**
	 * Class constructor.
	 *
	 * @param array $args Controller arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					'messages_resource'    => [
						'path' => '/messages',
						'rest' => true,
					],

					'message_send_action'  => [
						'base'   => 'messages_resource',
						'method' => 'POST',
						'action' => [ $this, 'send_message' ],
						'rest'   => true,
					],

					'messages_thread_page' => [
						'title'    => esc_html__( 'Messages', 'hivepress-messages' ),
						'base'     => 'user_account_page',
						'path'     => '/messages',
						'redirect' => [ $this, 'redirect_messages_thread_page' ],
						'action'   => [ $this, 'render_messages_thread_page' ],
					],

					'messages_view_page'   => [
						'base'     => 'messages_thread_page',
						'path'     => '/(?P<user_id>\d+)',
						'title'    => [ $this, 'get_messages_view_title' ],
						'redirect' => [ $this, 'redirect_messages_view_page' ],
						'action'   => [ $this, 'render_messages_view_page' ],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
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
		$form = ( new Forms\Message_Send() )->set_values( $request->get_params() );

		if ( ! $form->validate() ) {
			return hp\rest_error( 400, $form->get_errors() );
		}

		// Get sender.
		$sender_id = $request->get_param( 'sender' ) ? $request->get_param( 'sender' ) : get_current_user_id();

		$sender = Models\User::query()->get_by_id( $sender_id );

		if ( empty( $sender ) ) {
			return hp\rest_error( 400 );
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_users' ) && get_current_user_id() !== $sender->get_id() ) {
			return hp\rest_error( 403 );
		}

		// Get recipient.
		$recipient = Models\User::query()->get_by_id( $form->get_value( 'recipient' ) );

		if ( empty( $recipient ) ) {
			return hp\rest_error( 400 );
		}

		// Check recipient.
		if ( $recipient->get_id() === $sender->get_id() ) {
			return hp\rest_error( 403, esc_html__( 'You can\'t send messages to yourself.', 'hivepress-messages' ) );
		}

		// Get listing.
		if ( $form->get_value( 'listing' ) ) {
			$listing = Models\Listing::query()->get_by_id( $form->get_value( 'listing' ) );

			if ( empty( $listing ) || $listing->get_status() !== 'publish' ) {
				return hp\rest_error( 400 );
			}
		}

		// Add message.
		$message = ( new Models\Message() )->fill(
			array_merge(
				$form->get_values(),
				[
					'sender'               => $sender->get_id(),
					'sender__display_name' => $sender->get_display_name(),
					'sender__email'        => $sender->get_email(),
					'recipient'            => $recipient->get_id(),
				]
			)
		);

		// Set email arguments.
		$email_args = [
			'recipient' => $recipient->get_email(),

			'tokens'    => [
				'user_name'    => $recipient->get_display_name(),
				'message_text' => $message->get_text(),
			],
		];

		if ( $message->get_listing__id() ) {
			$email_args['subject'] = sprintf( hp\sanitize_html( __( 'New reply to "%s"', 'hivepress-messages' ) ), $message->get_listing__title() );
		} else {
			$email_args['subject'] = sprintf( hp\sanitize_html( __( 'New message from %s', 'hivepress-messages' ) ), $sender->get_display_name() );
		}

		if ( get_option( 'hp_message_enable_storage' ) ) {

			// Get expiration period.
			$expiration_period = absint( get_option( 'hp_message_expiration_period' ) );

			if ( $expiration_period ) {

				// Set expiration time.
				$message->set_expired_time( time() + $expiration_period * DAY_IN_SECONDS );
			}

			if ( ! $message->save() ) {
				return hp\rest_error( 400, $message->_get_errors() );
			}

			// Send email.
			( new Emails\Message_Send(
				hp\merge_arrays(
					$email_args,
					[
						'tokens' => [
							'message_url' => hivepress()->router->get_url( 'messages_view_page', [ 'user_id' => $sender->get_id() ] ),
						],
					]
				)
			) )->send();
		} else {

			// Send email.
			( new Emails\Message_Send(
				hp\merge_arrays(
					$email_args,
					[
						'body'    => '%message_text%',

						'headers' => [
							'reply-to' => $sender->get_display_name() . ' <' . $sender->get_email() . '>',
						],
					]
				)
			) )->send();
		}

		return hp\rest_response(
			201,
			[
				'id' => $message->get_id(),
			]
		);
	}

	/**
	 * Redirects messages thread page.
	 *
	 * @return mixed
	 */
	public function redirect_messages_thread_page() {
		global $wpdb;

		// Check permissions.
		if ( ! get_option( 'hp_message_enable_storage' ) ) {
			return true;
		}

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hivepress()->router->get_url(
				'user_login_page',
				[
					'redirect' => hivepress()->router->get_current_url(),
				]
			);
		}

		// Get cached message IDs.
		$message_ids = hivepress()->cache->get_user_cache( get_current_user_id(), 'todo', 'models/message' );

		if ( is_null( $message_ids ) ) {

			// Get message IDs.
			$message_ids = array_column(
				$wpdb->get_results(
					$wpdb->prepare(
						"SELECT comment_ID FROM {$wpdb->comments}
						WHERE comment_type = %s AND ( user_id = %d OR comment_karma = %d )
						GROUP BY user_id, comment_karma
						ORDER BY comment_date DESC;",
						'hp_message',
						get_current_user_id(),
						get_current_user_id()
					),
					ARRAY_A
				),
				'comment_ID'
			);

			// Cache message IDs.
			if ( count( $message_ids ) <= 1000 ) {
				hivepress()->cache->set_user_cache( get_current_user_id(), 'todo', 'models/message', $message_ids );
			}
		}

		// Check messages.
		if ( empty( $message_ids ) ) {
			return hivepress()->router->get_url( 'user_account_page' );
		}

		// Set request context.
		hivepress()->request->set_context( 'message_ids', $message_ids );

		return false;
	}

	/**
	 * Renders messages thread page.
	 *
	 * @return string
	 */
	public function render_messages_thread_page() {

		// Get message IDs.
		$message_ids = hivepress()->request->get_context( 'message_ids', [] );

		// Get messages.
		$messages = [];

		$all_messages = Models\Message::query()->filter(
			[
				'id__in' => $message_ids,
			]
		)->order( 'id__in' )
		->limit( count( $message_ids ) )
		->get()
		->serialize();

		foreach ( $all_messages as $message ) {
			if ( $message->get_sender__id() === get_current_user_id() ) {

				// Get recipient.
				$recipient = $message->get_recipient();

				// Set sender.
				$message->fill(
					[
						'sender'               => $recipient->get_id(),
						'sender__display_name' => $recipient->get_display_name(),
						'sender__email'        => $recipient->get_email(),
					]
				);
			}

			// Add message.
			if ( ! isset( $messages[ $message->get_sender__id() ] ) ) {
				$messages[ $message->get_sender__id() ] = $message;
			}
		}

		// Render template.
		return ( new Blocks\Template(
			[
				'template' => 'messages_thread_page',

				'context'  => [
					'messages' => $messages,
				],
			]
		) )->render();
	}

	/**
	 * Gets messages view title.
	 *
	 * @return string
	 */
	public function get_messages_view_title() {

		// Get user.
		$user = Models\User::query()->get_by_id( hivepress()->request->get_param( 'user_id' ) );

		// Set request context.
		hivepress()->request->set_context( 'message_user', $user );

		if ( $user ) {
			return sprintf( esc_html__( 'Messages from %s', 'hivepress-messages' ), $user->get_display_name() );
		}
	}

	/**
	 * Redirects messages view page.
	 *
	 * @return mixed
	 */
	public function redirect_messages_view_page() {
		global $wpdb;

		// Check permissions.
		if ( ! get_option( 'hp_message_enable_storage' ) ) {
			return true;
		}

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hivepress()->router->get_url(
				'user_login_page',
				[
					'redirect' => hivepress()->router->get_current_url(),
				]
			);
		}

		// Check user.
		$user = hivepress()->request->get_context( 'message_user' );

		if ( empty( $user ) || get_current_user_id() === $user->get_id() ) {
			return hivepress()->router->get_url( 'messages_thread_page' );
		}

		// Get cached message IDs.
		$message_ids = hivepress()->cache->get_user_cache( get_current_user_id(), 'todo2', 'models/message' );

		if ( is_null( $message_ids ) ) {

			// Get message IDs.
			$message_ids = array_column(
				$wpdb->get_results(
					$wpdb->prepare(
						"SELECT comment_ID FROM {$wpdb->comments}
						WHERE comment_type = %s AND ( ( user_id = %d AND comment_karma = %d ) OR ( user_id = %d AND comment_karma = %d ) )
						ORDER BY comment_date ASC;",
						'hp_message',
						get_current_user_id(),
						$user->get_id(),
						$user->get_id(),
						get_current_user_id()
					),
					ARRAY_A
				),
				'comment_ID'
			);

			// Cache message IDs.
			if ( count( $message_ids ) <= 1000 ) {
				hivepress()->cache->set_user_cache( get_current_user_id(), 'todo2', 'models/message', $message_ids );
			}
		}

		// Check messages.
		if ( empty( $message_ids ) ) {
			return hivepress()->router->get_url( 'messages_thread_page' );
		}

		// Set request context.
		hivepress()->request->set_context( 'message_ids', $message_ids );

		return false;
	}

	/**
	 * Renders messages view page.
	 *
	 * @return string
	 */
	public function render_messages_view_page() {

		// Get message IDs.
		$message_ids = hivepress()->request->get_context( 'message_ids', [] );

		// Get messages.
		$messages = Models\Message::query()->filter(
			[
				'id__in' => $message_ids,
			]
		)->order( 'id__in' )
		->limit( count( $message_ids ) )
		->get()
		->serialize();

		// Render template.
		return ( new Blocks\Template(
			[
				'template' => 'messages_view_page',

				'context'  => [
					'messages' => $messages,
				],
			]
		) )->render();
	}
}
