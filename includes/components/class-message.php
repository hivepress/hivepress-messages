<?php
/**
 * Message component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Emails;

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

		// Add menu items.
		add_filter( 'hivepress/v1/menus/account', [ $this, 'add_menu_items' ] );
	}

	/**
	 * Adds menu items.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function add_menu_items( $menu ) {

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

		if ( ! empty( $messages ) ) {

			// Add menu item.
			$menu['items']['view_chats'] = [
				'route' => 'message/view_chats',
				'order' => 30,
			];
		}

		return $menu;
	}
}
