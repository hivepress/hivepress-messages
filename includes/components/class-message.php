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
					'user_id' => get_current_user_id(),
					'fields'  => 'ids',
				]
			),
			get_comments(
				[
					'type'   => 'hp_message',
					'karma'  => get_current_user_id(),
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
			$menu['items']['select_messages'] = [
				'route' => 'message/select_messages',
				'order' => 30,
			];
		}

		return $menu;
	}
}
