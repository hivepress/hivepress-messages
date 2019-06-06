<?php
/**
 * Message thread block template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message thread block template class.
 *
 * @class Message_Thread_Block
 */
class Message_Thread_Block extends Template {

	/**
	 * Template name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Template blocks.
	 *
	 * @var array
	 */
	protected static $blocks = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Template arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'message_container' => [
						'type'       => 'container',
						'tag'        => 'tr',
						'order'      => 10,

						'attributes' => [
							'class' => [ 'hp-message', 'hp-message--thread-block' ],
						],

						'blocks'     => [
							'message_sender'  => [
								'type'     => 'element',
								'filepath' => 'message/thread/message-sender',
								'order'    => 10,
							],

							'message_listing' => [
								'type'     => 'element',
								'filepath' => 'message/thread/message-listing',
								'order'    => 20,
							],

							'message_date'    => [
								'type'     => 'element',
								'filepath' => 'message/thread/message-date',
								'order'    => 30,
							],
						],
					],
				],
			],
			$args,
			'blocks'
		);

		parent::init( $args );
	}
}
