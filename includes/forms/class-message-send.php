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
	 * Form message.
	 *
	 * @var string
	 */
	protected static $message;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $model;

	/**
	 * Form captcha.
	 *
	 * @var bool
	 */
	protected static $captcha = false;

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Form button.
	 *
	 * @var object
	 */
	protected static $button;

	/**
	 * Class initializer.
	 *
	 * @param array $args Form arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'title'   => esc_html__( 'Send Message', 'hivepress-messages' ),
				'message' => esc_html__( 'Message has been sent', 'hivepress-messages' ),
				'model'   => 'message',

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
				'action' => hp\get_rest_url( '/messages' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
