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
						'_order'     => 10,

						'attributes' => [
							'class' => [ 'hp-message', 'hp-message--thread-block' ],
						],

						'blocks'     => [
							'message_sender'  => [
								'type'   => 'part',
								'path'   => 'message/thread/message-sender',
								'_order' => 10,
							],

							'message_listing' => [
								'type'   => 'part',
								'path'   => 'message/thread/message-listing',
								'_order' => 20,
							],

							'message_date'    => [
								'type'   => 'part',
								'path'   => 'message/thread/message-date',
								'_order' => 30,
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
