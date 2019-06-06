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
						'order'      => 10,

						'attributes' => [
							'class' => [ 'hp-message', 'hp-message--view-block' ],
						],

						'blocks'     => [
							'message_header'  => [
								'type'       => 'container',
								'tag'        => 'header',
								'order'      => 10,

								'attributes' => [
									'class' => [ 'hp-message__header' ],
								],

								'blocks'     => [
									'message_listing' => [
										'type'     => 'element',
										'filepath' => 'message/view/message-listing',
										'order'    => 10,
									],

									'message_details' => [
										'type'       => 'container',
										'order'      => 20,

										'attributes' => [
											'class' => [ 'hp-message__details' ],
										],

										'blocks'     => [
											'message_sender' => [
												'type'     => 'element',
												'filepath' => 'message/view/message-sender',
												'order'    => 10,
											],

											'message_date' => [
												'type'     => 'element',
												'filepath' => 'message/view/message-date',
												'order'    => 20,
											],
										],
									],
								],
							],

							'message_content' => [
								'type'       => 'container',
								'order'      => 20,

								'attributes' => [
									'class' => [ 'hp-message__content' ],
								],

								'blocks'     => [
									'message_text' => [
										'type'     => 'element',
										'filepath' => 'message/view/message-text',
										'order'    => 10,
									],
								],
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
