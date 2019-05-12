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

		// todo.
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
