<?php
/**
 * Message model.
 *
 * @package HivePress\Models
 */

namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message model class.
 *
 * @class Message
 */
class Message extends Comment {

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Model fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Model aliases.
	 *
	 * @var array
	 */
	protected static $aliases = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Model arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields'  => [
					'text'         => [
						'label'      => esc_html__( 'Message', 'hivepress-messages' ),
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
					],

					'date'         => [
						'type'       => 'text',
						'max_length' => 128,
					],

					'sender_name'  => [
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
					],

					'sender_email' => [
						'type'     => 'email',
						'required' => true,
					],

					'sender_id'    => [
						'type'      => 'number',
						'min_value' => 0,
						'required'  => true,
					],

					'recipient_id' => [
						'type'      => 'number',
						'min_value' => 0,
						'required'  => true,
					],

					'listing_id'   => [
						'type'      => 'number',
						'min_value' => 0,
					],
				],

				'aliases' => [
					'comment_content'      => 'text',
					'comment_date'         => 'date',
					'comment_author'       => 'sender_name',
					'comment_author_email' => 'sender_email',
					'user_id'              => 'sender_id',
					'comment_karma'        => 'recipient_id',
					'comment_post_ID'      => 'listing_id',
				],
			],
			$args
		);

		parent::init( $args );
	}

	/**
	 * Gets date.
	 *
	 * @param string $format Date format.
	 * @return string
	 */
	final public function get_date( $format = null ) {
		if ( is_null( $format ) ) {
			$format = get_option( 'date_format' );
		}

		return date_i18n( $format, strtotime( $this->attributes['date'] ) );
	}
}
