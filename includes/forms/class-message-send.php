<?php
/**
 * Message send form.
 *
 * @package HivePress\Forms
 */

namespace HivePress\Forms;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message send form class.
 *
 * @class Message_Send
 */
class Message_Send extends Model_Form {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'   => hivepress()->translator->get_string( 'send_message' ),
				'captcha' => false,
				'model'   => 'message',
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Form arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'message' => esc_html__( 'Your message has been sent.', 'hivepress-messages' ),
				'action'  => hivepress()->router->get_url( 'message_send_action' ),

				'fields'  => [
					'text'      => [
						'_order' => 10,
					],

					'recipient' => [
						'display_type' => 'hidden',
					],

					'listing'   => [
						'display_type' => 'hidden',
					],
				],

				'button'  => [
					'label' => hivepress()->translator->get_string( 'send_message' ),
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
