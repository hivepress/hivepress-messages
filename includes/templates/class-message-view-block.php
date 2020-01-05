<?php
/**
 * Message view block template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message view block template class.
 *
 * @class Message_View_Block
 */
class Message_View_Block extends Template {

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
						'_order'     => 10,

						'attributes' => [
							'class' => [ 'hp-message', 'hp-message--view-block' ],
						],

						'blocks'     => [
							'message_header'  => [
								'type'       => 'container',
								'tag'        => 'header',
								'_order'     => 10,

								'attributes' => [
									'class' => [ 'hp-message__header' ],
								],

								'blocks'     => [
									'message_listing' => [
										'type'   => 'part',
										'path'   => 'message/view/message-listing',
										'_order' => 10,
									],

									'message_details' => [
										'type'       => 'container',
										'_order'     => 20,

										'attributes' => [
											'class' => [ 'hp-message__details' ],
										],

										'blocks'     => [
											'message_sender' => [
												'type'   => 'part',
												'path'   => 'message/view/message-sender',
												'_order' => 10,
											],

											'message_date' => [
												'type'   => 'part',
												'path'   => 'message/view/message-date',
												'_order' => 20,
											],
										],
									],
								],
							],

							'message_content' => [
								'type'       => 'container',
								'_order'     => 20,

								'attributes' => [
									'class' => [ 'hp-message__content' ],
								],

								'blocks'     => [
									'message_text' => [
										'type'   => 'part',
										'path'   => 'message/view/message-text',
										'_order' => 10,
									],
								],
							],
						],
					],
				],
			],
			$args
		);

		parent::init( $args );
	}
}
