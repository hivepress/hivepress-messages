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

					'content'      => [
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
					],
				],

				'aliases' => [
					'user_id'         => 'sender_id',
					'comment_karma'   => 'recipient_id',
					'comment_post_ID' => 'listing_id',
				],
			],
			$args
		);

		parent::init( $args );
	}
}
