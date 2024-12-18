<?php
/**
 * Message component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Blocks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message component class.
 *
 * @class Message
 */
final class Message extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Validate message.
		add_filter( 'hivepress/v1/models/message/errors', [ $this, 'validate_message' ], 10, 2 );

		// Allow message attachment.
		add_filter( 'option_hp_message_allow_attachment', [ $this, 'allow_message_attachment' ] );

		if ( get_option( 'hp_message_allow_attachment' ) ) {

			// Add message fields.
			add_filter( 'hivepress/v1/models/message', [ $this, 'add_message_fields' ] );
			add_filter( 'hivepress/v1/forms/message_send', [ $this, 'add_message_fields' ] );
		}

		if ( get_option( 'hp_message_enable_storage' ) ) {

			// Delete messages.
			add_action( 'hivepress/v1/events/daily', [ $this, 'delete_old_messages' ] );
			add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_user_messages' ] );

			// Clear message cache.
			add_action( 'hivepress/v1/models/message/create', [ $this, 'clear_message_cache' ], 10, 2 );
			add_action( 'hivepress/v1/models/message/update', [ $this, 'clear_message_cache' ], 10, 2 );
			add_action( 'hivepress/v1/models/message/delete', [ $this, 'clear_message_cache' ], 10, 2 );
		}

		if ( ! is_admin() ) {

			// Set request context.
			add_action( 'init', [ $this, 'set_request_context' ], 100 );

			// Alter account menu.
			add_filter( 'hivepress/v1/menus/user_account', [ $this, 'alter_account_menu' ] );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/messages_view_page/blocks', [ $this, 'alter_messages_view_blocks' ], 10, 2 );

			add_filter( 'hivepress/v1/templates/message_view_block/blocks', [ $this, 'alter_message_view_blocks' ], 10, 2 );
			add_filter( 'hivepress/v1/templates/message_thread_block/blocks', [ $this, 'alter_message_view_blocks' ], 10, 2 );

			add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_block' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );

			add_filter( 'hivepress/v1/templates/vendor_view_block', [ $this, 'alter_vendor_view_block' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_page' ] );

			add_filter( 'hivepress/v1/templates/user_view_block', [ $this, 'alter_user_view_block' ] );
			add_filter( 'hivepress/v1/templates/user_view_page', [ $this, 'alter_user_view_page' ] );

			if ( hivepress()->get_version( 'marketplace' ) ) {
				add_filter( 'hivepress/v1/templates/order_footer_block', [ $this, 'alter_order_footer_block' ] );
			}

			if ( hivepress()->get_version( 'bookings' ) ) {
				add_filter( 'hivepress/v1/templates/booking_view_block', [ $this, 'alter_booking_view_block' ] );
				add_filter( 'hivepress/v1/templates/booking_view_page', [ $this, 'alter_booking_view_page' ] );
			}
		}

		parent::__construct( $args );
	}

	/**
	 * Gets message draft.
	 *
	 * @return object
	 */
	public function get_message_draft() {
		$draft = hivepress()->request->get_context( 'message_draft' );

		if ( ! $draft ) {

			// Get cached draft ID.
			$draft_id = hivepress()->cache->get_user_cache( get_current_user_id(), 'draft_id', 'models/message' );

			if ( is_null( $draft_id ) ) {

				// Get draft ID.
				$draft_id = Models\Message::query()->filter(
					[
						'sender'    => get_current_user_id(),
						'recipient' => 0,
					]
				)->get_first_id();

				if ( ! $draft_id ) {

					// Add draft.
					$draft_id = (int) wp_insert_comment(
						[
							'comment_type'  => 'hp_message',
							'user_id'       => get_current_user_id(),
							'comment_karma' => 0,
						]
					);
				}

				// Cache draft ID.
				if ( $draft_id ) {
					hivepress()->cache->set_user_cache( get_current_user_id(), 'draft_id', 'models/message', $draft_id );
				}
			}

			if ( $draft_id ) {

				// Get draft.
				$draft = Models\Message::query()->get_by_id( $draft_id );

				// Set request context.
				hivepress()->request->set_context( 'message_draft', $draft );
			}
		}

		return $draft;
	}

	/**
	 * Validates message.
	 *
	 * @param array  $errors Error messages.
	 * @param object $message Message object.
	 * @return array
	 */
	public function validate_message( $errors, $message ) {
		if ( ! $message->get_id() && empty( $errors ) ) {

			// Get keywords.
			$keywords = get_option( 'hp_message_blocked_keywords' );

			if ( $keywords ) {
				$keywords = array_filter( array_map( 'trim', explode( "\n", $keywords ) ) );

				// Check keywords.
				foreach ( $keywords as $keyword ) {
					if ( preg_match( '/' . preg_quote( $keyword, '/' ) . '/i', $message->get_text() ) ) {

						// Add error.
						$errors[] = esc_html__( 'Your message contains inappropriate content.', 'hivepress-messages' );

						break;
					}
				}
			}
		}

		return $errors;
	}

	/**
	 * Allows message attachment.
	 *
	 * @param mixed $value Option value.
	 * @return bool
	 */
	public function allow_message_attachment( $value ) {
		return $value && get_option( 'hp_message_enable_storage' );
	}

	/**
	 * Adds message fields.
	 *
	 * @param array $form Form arguments.
	 * @return array
	 */
	public function add_message_fields( $form ) {

		// Get file formats.
		$formats = hivepress()->request->get_context( 'message_attachment_types' );

		if ( ! is_array( $formats ) ) {
			$formats = array_filter( explode( '|', implode( '|', (array) get_option( 'hp_message_attachment_types' ) ) ) );

			hivepress()->request->set_context( 'message_attachment_types', $formats );
		}

		// Add attachment field.
		$form['fields']['attachment'] = [
			'label'     => esc_html__( 'Attachment', 'hivepress-messages' ),
			'type'      => 'attachment_upload',
			'formats'   => $formats,
			'protected' => true,
			'_model'    => 'attachment',
			'_external' => true,
			'_order'    => 20,
		];

		return $form;
	}

	/**
	 * Deletes old messages.
	 */
	public function delete_old_messages() {

		// @deprecated since version 1.2.1.
		if ( get_option( 'hp_message_expiration_period' ) ) {
			update_option( 'hp_message_storage_period', get_option( 'hp_message_expiration_period' ) );

			delete_option( 'hp_message_expiration_period' );
		}

		// Get storage period.
		$storage_period = absint( get_option( 'hp_message_storage_period' ) );

		// @deprecated since core version 1.3.4.
		if ( $storage_period ) {

			// Get message IDs.
			$message_ids = get_comments(
				[
					'type'       => 'hp_message',

					'date_query' => [
						[
							'before' => date( 'Y-m-d H:i:s', time() - $storage_period * DAY_IN_SECONDS ),
						],
					],
				]
			);

			// Delete messages.
			foreach ( $message_ids as $message_id ) {
				wp_delete_comment( $message_id, true );
			}
		}

		// Delete message drafts.
		if ( get_option( 'hp_message_allow_attachment' ) ) {
			Models\Message::query()->filter(
				[
					'recipient' => 0,
				]
			)->delete();
		}

		// Delete message cache.
		if ( get_option( 'hp_message_allow_monitoring' ) ) {
			foreach ( get_users(
				[
					'role'   => 'administrator',
					'fields' => 'ids',
				]
			) as $user_id ) {
				hivepress()->cache->delete_user_cache( $user_id, null, 'models/message' );
			}
		}
	}

	/**
	 * Deletes user messages.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_user_messages( $user_id ) {
		Models\Message::query()->filter(
			[
				'sender' => $user_id,
			]
		)->delete();

		Models\Message::query()->filter(
			[
				'recipient' => $user_id,
			]
		)->delete();
	}

	/**
	 * Clears message cache.
	 *
	 * @param int    $message_id Message ID.
	 * @param object $message Message object.
	 */
	public function clear_message_cache( $message_id, $message ) {
		hivepress()->cache->delete_user_cache( $message->get_recipient__id(), null, 'models/message' );
	}

	/**
	 * Sets request context.
	 */
	public function set_request_context() {
		global $wpdb;

		// Check permissions.
		if ( ! get_option( 'hp_message_enable_storage' ) ) {
			return;
		}

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Get cached thread IDs.
		$thread_ids = hivepress()->cache->get_user_cache( get_current_user_id(), 'thread_ids', 'models/message' );

		if ( is_null( $thread_ids ) ) {

			// Get thread query.
			$thread_query = null;

			if ( get_option( 'hp_message_allow_monitoring' ) && current_user_can( 'manage_options' ) ) {
				$thread_query = $wpdb->prepare(
					"SELECT MAX(comment_ID) AS comment_ID FROM {$wpdb->comments}
					WHERE comment_type = %s AND comment_karma != 0
					GROUP BY user_id, comment_karma;",
					'hp_message'
				);
			} else {
				$thread_query = $wpdb->prepare(
					"SELECT MAX(comment_ID) AS comment_ID FROM {$wpdb->comments}
					WHERE comment_type = %s AND ( user_id = %d OR comment_karma = %d ) AND comment_karma != 0
					GROUP BY user_id, comment_karma;",
					'hp_message',
					get_current_user_id(),
					get_current_user_id()
				);
			}

			// Get thread IDs.
			$thread_ids = array_column(
				$wpdb->get_results(
					$thread_query,
					ARRAY_A
				),
				'comment_ID'
			);

			// Cache thread IDs.
			if ( count( $thread_ids ) <= 1000 ) {
				hivepress()->cache->set_user_cache( get_current_user_id(), 'thread_ids', 'models/message', $thread_ids );
			}
		}

		// Set request context.
		hivepress()->request->set_context( 'message_thread_ids', $thread_ids );

		// Check thread IDs.
		if ( ! $thread_ids ) {
			return;
		}

		// Get cached unread count.
		$unread_count = hivepress()->cache->get_user_cache( get_current_user_id(), 'unread_count', 'models/message' );

		if ( is_null( $unread_count ) ) {

			// Get unread count.
			$unread_count = absint(
				$wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->comments}
						WHERE comment_type = %s AND comment_karma = %d
						AND comment_approved = %s",
						'hp_message',
						get_current_user_id(),
						'0'
					)
				)
			);

			// Cache unread count.
			hivepress()->cache->set_user_cache( get_current_user_id(), 'unread_count', 'models/message', $unread_count );
		}

		// Set request context.
		if ( $unread_count ) {
			hivepress()->request->set_context( 'message_unread_count', $unread_count );
			hivepress()->request->set_context( 'notice_count', (int) hivepress()->request->get_context( 'notice_count' ) + $unread_count );
		}
	}

	/**
	 * Alters account menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_account_menu( $menu ) {
		if ( hivepress()->request->get_context( 'message_thread_ids' ) ) {
			$menu_item = [
				'route'  => 'messages_thread_page',
				'_order' => 30,
			];

			if ( hivepress()->request->get_context( 'message_unread_count' ) ) {
				$menu_item['meta'] = hivepress()->request->get_context( 'message_unread_count' );
			}

			$menu['items']['messages_thread'] = $menu_item;
		}

		return $menu;
	}

	/**
	 * Alters messages view blocks.
	 *
	 * @param array  $blocks Block arguments.
	 * @param object $template Template object.
	 * @return array
	 */
	public function alter_messages_view_blocks( $blocks, $template ) {

		// Get recipient.
		$recipient = $template->get_context( 'recipient' );

		if ( $recipient && get_current_user_id() !== $recipient->get_id() ) {
			$blocks = hp\merge_trees(
				[ 'blocks' => $blocks ],
				[
					'blocks' => [
						'message_send_form' => [
							'type' => 'content',
						],
					],
				]
			)['blocks'];
		}

		return $blocks;
	}

	/**
	 * Alters message view blocks.
	 *
	 * @param array  $blocks Block arguments.
	 * @param object $template Template object.
	 * @return array
	 */
	public function alter_message_view_blocks( $blocks, $template ) {

		// Get message and recipient.
		$message   = $template->get_context( 'message' );
		$recipient = $template->get_context( 'recipient' );

		if ( $message ) {

			// Get CSS classes.
			$classes = [];

			if ( $message->is_read() ) {
				$classes[] = 'hp-message--read';
			}

			if ( $recipient ) {
				if ( $message->get_sender__id() === $recipient->get_id() ) {
					$classes[] = 'hp-message--sent';
				}

				if ( current_user_can( 'manage_options' ) || ( get_option( 'hp_message_allow_deletion' ) && get_current_user_id() === $message->get_sender__id() ) ) {

					// Add delete link.
					$blocks = hp\merge_trees(
						[ 'blocks' => $blocks ],
						[
							'blocks' => [
								'message_header' => [
									'blocks' => [
										'message_delete_modal' => [
											'type'        => 'modal',
											'model'       => 'message',
											'title'       => esc_html__( 'Delete Message', 'hivepress-messages' ),
											'_capability' => 'read',
											'_order'      => 5,

											'blocks'      => [
												'message_delete_form' => [
													'type' => 'form',
													'form' => 'message_delete',
													'_order' => 10,
												],
											],
										],

										'message_delete_link'  => [
											'type'   => 'part',
											'path'   => 'message/view/message-delete-link',
											'_order' => 5,
										],
									],
								],
							],
						]
					)['blocks'];
				}
			}

			// Add HTML attributes.
			$blocks = hp\merge_trees(
				[ 'blocks' => $blocks ],
				[
					'blocks' => [
						'message_container' => [
							'attributes' => [
								'id'    => 'message-' . $message->get_id(),
								'class' => $classes,
							],
						],
					],
				]
			)['blocks'];
		}

		return $blocks;
	}

	/**
	 * Alters listing view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'listing_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'listing',
								'title'       => hivepress()->translator->get_string( 'reply_to_listing' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'listing/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters listing view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'listing_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'listing',
								'title'       => hivepress()->translator->get_string( 'reply_to_listing' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'listing/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'vendor',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'vendor/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'vendor',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'vendor/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters user view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_user_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'user_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'user',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'user/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters user view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_user_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'user_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'user',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'user/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters order footer block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_order_footer_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'order_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'order/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters booking view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_booking_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'booking_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'booking',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'booking/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters booking view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_booking_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'booking_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'        => 'modal',
								'model'       => 'booking',
								'title'       => hivepress()->translator->get_string( 'send_message' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'message_send_form' => [
										'type'   => 'message_send_form',
										'_order' => 10,
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'booking/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}
}
