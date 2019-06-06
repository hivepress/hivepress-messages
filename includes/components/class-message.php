<?php
/**
 * Message component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message component class.
 *
 * @class Message
 */
final class Message {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Delete messages.
		add_action( 'delete_user', [ $this, 'delete_messages' ] );

		if ( ! is_admin() ) {

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_block' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_page' ] );

			// Set page title.
			add_filter( 'hivepress/v1/controllers/message/routes/view_messages', [ $this, 'set_page_title' ] );

			// Add menu items.
			add_filter( 'hivepress/v1/menus/account', [ $this, 'add_menu_items' ] );
		}
	}

	/**
	 * Deletes messages.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_messages( $user_id ) {

		// Get message IDs.
		$message_ids = array_merge(
			get_comments(
				[
					'type'    => 'hp_message',
					'user_id' => $user_id,
					'fields'  => 'ids',
				]
			),
			get_comments(
				[
					'type'   => 'hp_message',
					'karma'  => $user_id,
					'fields' => 'ids',
				]
			)
		);

		// Delete messages.
		foreach ( $message_ids as $message_id ) {
			wp_delete_comment( $message_id, true );
		}
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
								'type'    => 'modal',
								'model'   => 'listing',
								'caption' => esc_html__( 'Reply to Listing', 'hivepress-messages' ),

								'blocks'  => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'order'      => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_link'  => [
								'type'     => 'element',
								'filepath' => 'listing/view/block/message-send-link',
								'order'    => 10,
							],
						],
					],
				],
			],
			'blocks'
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
							'message_send_modal'  => [
								'type'    => 'modal',
								'caption' => esc_html__( 'Reply to Listing', 'hivepress-messages' ),

								'blocks'  => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'order'      => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_button' => [
								'type'     => 'element',
								'filepath' => 'listing/view/page/message-send-link',
								'order'    => 10,
							],
						],
					],
				],
			],
			'blocks'
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
							'message_send_modal'  => [
								'type'    => 'modal',
								'caption' => esc_html__( 'Send Message', 'hivepress-messages' ),

								'blocks'  => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'order'      => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_button' => [
								'type'     => 'element',
								'filepath' => 'vendor/view/page/message-send-link',
								'order'    => 10,
							],
						],
					],
				],
			],
			'blocks'
		);
	}

	/**
	 * Sets page title.
	 *
	 * @param array $route Route arguments.
	 * @return array
	 */
	public function set_page_title( $route ) {
		$user = get_userdata( get_query_var( 'hp_user_id' ) );

		if ( false !== $user ) {
			$route['title'] = sprintf( esc_html__( 'Messages from %s', 'hivepress-messages' ), $user->display_name );
		}

		return $route;
	}

	/**
	 * Adds menu items.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function add_menu_items( $menu ) {

		// Check messages.
		$message_ids = array_merge(
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

		if ( ! empty( $message_ids ) ) {

			// Add menu item.
			$menu['items']['thread_messages'] = [
				'route' => 'message/thread_messages',
				'order' => 30,
			];
		}

		return $menu;
	}
}
