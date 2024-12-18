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
					'messages_resource'     => [
						'path' => '/messages',
						'rest' => true,
					],

					'message_resource'      => [
						'base' => 'messages_resource',
						'path' => '/(?P<message_id>\d+)',
						'rest' => true,
					],

					'message_send_action'   => [
						'base'   => 'messages_resource',
						'method' => 'POST',
						'action' => [ $this, 'send_message' ],
						'rest'   => true,
					],

					'message_delete_action' => [
						'base'   => 'message_resource',
						'method' => 'DELETE',
						'action' => [ $this, 'delete_message' ],
						'rest'   => true,
					],

					'messages_read_action'  => [
						'base'   => 'messages_resource',
						'path'   => '/read',
						'method' => 'GET',
						'action' => [ $this, 'read_messages' ],
						'rest'   => true,
					],

					'messages_thread_page'  => [
						'title'    => hivepress()->translator->get_string( 'messages' ),
						'base'     => 'user_account_page',
						'path'     => '/messages',
						'redirect' => [ $this, 'redirect_messages_thread_page' ],
						'action'   => [ $this, 'render_messages_thread_page' ],
					],

					'messages_view_page'    => [
						'base'     => 'messages_thread_page',
						'path'     => '/(?P<user_id>\d+)/?(?P<recipient_id>\d+)?',
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

		// Get sender ID.
		$sender_id = $request->get_param( 'sender' ) ? $request->get_param( 'sender' ) : get_current_user_id();

		// Get sender.
		$sender = Models\User::query()->get_by_id( $sender_id );

		if ( ! $sender ) {
			return hp\rest_error( 400 );
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_users' ) && get_current_user_id() !== $sender->get_id() ) {
			return hp\rest_error( 403 );
		}

		// Get recipient.
		$recipient = Models\User::query()->get_by_id( $form->get_value( 'recipient' ) );

		if ( ! $recipient ) {
			return hp\rest_error( 400 );
		}

		// Check recipient.
		if ( $recipient->get_id() === $sender->get_id() ) {
			return hp\rest_error( 403, esc_html__( 'You can\'t send messages to yourself.', 'hivepress-messages' ) );
		}

		// Get listing.
		if ( $form->get_value( 'listing' ) ) {
			$listing = Models\Listing::query()->get_by_id( $form->get_value( 'listing' ) );

			if ( ! $listing || $listing->get_status() !== 'publish' ) {
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
					'read'                 => 0,
				]
			)
		);

		if ( get_option( 'hp_message_allow_attachment' ) ) {

			// Get message draft.
			$message_draft = hivepress()->message->get_message_draft();

			if ( $message_draft && $message_draft->get_attachment__id() ) {

				// Get attachments.
				$attachments = $message_draft->get_attachment();

				if ( ! is_array( $attachments ) ) {
					$attachments = [ $attachments ];
				}

				if ( $attachments ) {

					// Set attachment.
					$message->set_attachment( $message_draft->get_attachment__id() );
				}
			}
		}

		// Set email arguments.
		$email_args = [
			'recipient' => $recipient->get_email(),

			'tokens'    => [
				'sender'       => $sender,
				'recipient'    => $recipient,
				'message'      => $message,
				'user_name'    => $recipient->get_display_name(),
				'message_text' => $message->display_text(),
			],
		];

		if ( $message->get_listing__id() ) {
			$email_args['subject'] = sprintf( hp\sanitize_html( __( 'New reply to "%s"', 'hivepress-messages' ) ), $message->get_listing__title() );
		} else {
			$email_args['subject'] = sprintf( hp\sanitize_html( __( 'New message from %s', 'hivepress-messages' ) ), $sender->get_display_name() );
		}

		if ( get_option( 'hp_message_enable_storage' ) ) {
			if ( ! $message->save() ) {
				return hp\rest_error( 400, $message->_get_errors() );
			}

			// Set attachments.
			if ( isset( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					$attachment->set_parent( $message->get_id() )->save_parent();
				}

				$message_draft->set_attachment( null )->save_attachment();
			}

			// Send email.
			( new Emails\Message_Send(
				hp\merge_arrays(
					$email_args,
					[
						'tokens' => [
							'message_url' => hivepress()->router->get_url( 'messages_view_page', [ 'user_id' => $sender->get_id() ] ) . '#message-' . $message->get_id(),
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

		// Set response.
		$response = [
			'id' => $message->get_id(),
		];

		if ( $request->get_param( '_render' ) ) {

			// Fetch message.
			$message = Models\Message::query()->get_by_id( $message->get_id() );

			if ( $message ) {

				// Render message.
				$response['html'] = '<div class="hp-grid__item">';

				$response['html'] .= ( new Blocks\Template(
					[
						'template' => 'message_view_block',

						'context'  => [
							'message'   => $message,
							'recipient' => $sender,
						],
					]
				) )->render();

				$response['html'] .= '</div>';
			}
		}

		return hp\rest_response( 201, $response );
	}

	/**
	 * Deletes message.
	 *
	 * @param WP_REST_Request $request API request.
	 * @return WP_Rest_Response
	 */
	public function delete_message( $request ) {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hp\rest_error( 401 );
		}

		// Get message.
		$message = Models\Message::query()->get_by_id( $request->get_param( 'message_id' ) );

		if ( ! $message ) {
			return hp\rest_error( 404 );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) && ( ! get_option( 'hp_message_allow_deletion' ) || get_current_user_id() !== $message->get_sender__id() ) ) {
			return hp\rest_error( 403 );
		}

		// Delete message.
		if ( ! $message->delete() ) {
			return hp\rest_error( 400 );
		}

		return hp\rest_response( 204 );
	}

	/**
	 * Reads messages.
	 *
	 * @param WP_REST_Request $request API request.
	 * @return WP_Rest_Response
	 */
	public function read_messages( $request ) {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hp\rest_error( 401 );
		}

		// Get recipient.
		$recipient_id = absint( $request->get_param( 'recipient' ) );

		if ( get_current_user_id() !== $recipient_id ) {
			return hp\rest_error( 403 );
		}

		$recipient = Models\User::query()->get_by_id( $recipient_id );

		if ( ! $recipient ) {
			return hp\rest_error( 404 );
		}

		// Get sender.
		$sender_id = absint( $request->get_param( 'sender' ) );

		if ( ! $sender_id || $sender_id === $recipient_id ) {
			return hp\rest_error( 400 );
		}

		$sender = Models\User::query()->get_by_id( $sender_id );

		if ( ! $sender ) {
			return hp\rest_error( 404 );
		}

		// Get messages.
		$messages = Models\Message::query()->filter(
			[
				'sender'    => $sender->get_id(),
				'recipient' => $recipient->get_id(),
				'read'      => 0,
			]
		)->order( [ 'sent_date' => 'asc' ] )
		->get()
		->serialize();

		// Set response.
		$response = [
			'results' => [],
		];

		if ( $messages ) {
			foreach ( $messages as $message ) {

				// Add result.
				$response['results'][] = [
					'id' => $message->get_id(),
				];

				// Update message.
				$message->set_read( 1 )->save_read();
			}

			if ( $request->get_param( '_render' ) ) {

				// Render messages.
				$response['html'] = '';

				foreach ( $messages as $message ) {
					$response['html'] .= '<div class="hp-grid__item">';

					$response['html'] .= ( new Blocks\Template(
						[
							'template' => 'message_view_block',

							'context'  => [
								'message'   => $message,
								'recipient' => $recipient,
							],
						]
					) )->render();

					$response['html'] .= '</div>';
				}
			}
		}

		return hp\rest_response( 200, $response );
	}

	/**
	 * Redirects messages thread page.
	 *
	 * @return mixed
	 */
	public function redirect_messages_thread_page() {

		// Check permissions.
		if ( ! get_option( 'hp_message_enable_storage' ) ) {
			return true;
		}

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hivepress()->router->get_return_url( 'user_login_page' );
		}

		// Check threads.
		if ( ! hivepress()->request->get_context( 'message_thread_ids' ) ) {
			return hivepress()->router->get_url( 'user_account_page' );
		}

		return false;
	}

	/**
	 * Renders messages thread page.
	 *
	 * @return string
	 */
	public function render_messages_thread_page() {

		// Get thread IDs.
		$thread_ids = hivepress()->request->get_context( 'message_thread_ids', [] );

		// Get threads.
		$threads = [];

		$messages = Models\Message::query()->filter(
			[
				'id__in' => $thread_ids,
			]
		)->order( [ 'sent_date' => 'desc' ] )
		->limit( count( $thread_ids ) )
		->get()
		->serialize();

		foreach ( $messages as $message ) {
			if ( $message->get_sender__id() === get_current_user_id() ) {

				// Get recipient.
				$recipient = $message->get_recipient();

				if ( ! $recipient ) {
					continue;
				}

				// Set sender.
				$message->fill(
					[
						'sender'               => $recipient->get_id(),
						'sender__display_name' => $recipient->get_display_name(),
						'sender__email'        => $recipient->get_email(),
						'recipient'            => $message->get_sender__id(),
						'read'                 => 1,
					]
				);
			}

			// Get thread key.
			$thread_key = [ $message->get_sender__id(), $message->get_recipient__id() ];

			sort( $thread_key );

			$thread_key = implode( '-', $thread_key );

			// Add thread.
			if ( ! isset( $threads[ $thread_key ] ) ) {
				$threads[ $thread_key ] = $message;
			}
		}

		// Render template.
		return ( new Blocks\Template(
			[
				'template' => 'messages_thread_page',

				'context'  => [
					'messages' => $threads,
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
		$title = null;

		// Get sender.
		$sender = Models\User::query()->get_by_id( hivepress()->request->get_param( 'user_id' ) );

		// Get recipient.
		$recipient = hivepress()->request->get_user();

		if ( get_option( 'hp_message_allow_monitoring' ) && current_user_can( 'manage_options' ) && hivepress()->request->get_param( 'recipient_id' ) ) {
			$recipient = Models\User::query()->get_by_id( hivepress()->request->get_param( 'recipient_id' ) );
		}

		// Set request context.
		hivepress()->request->set_context( 'message_sender', $sender );
		hivepress()->request->set_context( 'message_recipient', $recipient );

		if ( $sender ) {
			if ( $recipient && get_current_user_id() !== $recipient->get_id() ) {

				/* translators: 1: sender name, 2: recipient name. */
				$title = sprintf( esc_html__( 'Messages from %1$s to %2$s', 'hivepress-messages' ), $sender->get_display_name(), $recipient->get_display_name() );
			} else {

				/* translators: %s: sender name. */
				$title = sprintf( esc_html__( 'Messages from %s', 'hivepress-messages' ), $sender->get_display_name() );
			}
		}

		return $title;
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
			return hivepress()->router->get_return_url( 'user_login_page' );
		}

		// Get sender and recipient.
		$sender    = hivepress()->request->get_context( 'message_sender' );
		$recipient = hivepress()->request->get_context( 'message_recipient' );

		if ( ! $sender || ! $recipient || $recipient->get_id() === $sender->get_id() ) {
			return hivepress()->router->get_url( 'messages_thread_page' );
		}

		// Get cached message IDs.
		$message_ids = hivepress()->cache->get_user_cache(
			$recipient->get_id(),
			[
				'fields'  => 'ids',
				'user_id' => $sender->get_id(),
			],
			'models/message'
		);

		if ( is_null( $message_ids ) ) {

			// Get message IDs.
			$message_ids = array_column(
				$wpdb->get_results(
					$wpdb->prepare(
						"SELECT comment_ID FROM {$wpdb->comments}
						WHERE comment_type = %s AND ( ( user_id = %d AND comment_karma = %d ) OR ( user_id = %d AND comment_karma = %d ) )
						ORDER BY comment_date ASC;",
						'hp_message',
						$recipient->get_id(),
						$sender->get_id(),
						$sender->get_id(),
						$recipient->get_id()
					),
					ARRAY_A
				),
				'comment_ID'
			);

			// Cache message IDs.
			if ( count( $message_ids ) <= 1000 ) {
				hivepress()->cache->set_user_cache(
					$recipient->get_id(),
					[
						'fields'  => 'ids',
						'user_id' => $sender->get_id(),
					],
					'models/message',
					$message_ids
				);
			}

			if ( $message_ids && get_current_user_id() === $recipient->get_id() ) {

				// Read messages.
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->comments} SET comment_approved = %s
						WHERE comment_type = %s AND user_id = %d AND comment_karma = %d;",
						'1',
						'hp_message',
						$sender->get_id(),
						$recipient->get_id()
					)
				);

				// Delete cache.
				hivepress()->cache->delete_user_cache( $recipient->get_id(), 'unread_count', 'models/message' );
			}
		}

		// Check messages.
		if ( ! $message_ids ) {
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
					'messages'  => $messages,
					'sender'    => hivepress()->request->get_context( 'message_sender' ),
					'recipient' => hivepress()->request->get_context( 'message_recipient' ),
				],
			]
		) )->render();
	}
}
