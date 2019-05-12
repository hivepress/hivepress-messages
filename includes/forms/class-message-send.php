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
	 * Form name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Form title.
	 *
	 * @var string
	 */
	protected static $title;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $model;

	/**
	 * Class initializer.
	 *
	 * @param array $args Form arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'title' => esc_html__( 'Send Message', 'hivepress-messages' ),
				'model' => 'message',
			],
			$args
		);

		parent::init( $args );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Form arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'message' => esc_html__( 'Message has been sent', 'hivepress-messages' ),
				'action'  => hp\get_rest_url( '/messages' ),

				'fields'  => [
					'text'         => [
						'order' => 10,
					],

					'recipient_id' => [
						'type' => 'hidden',
					],

					'listing_id'   => [
						'type' => 'hidden',
					],
				],

				'button'  => [
					'label' => esc_html__( 'Send Message', 'hivepress-messages' ),
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
