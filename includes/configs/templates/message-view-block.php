<?php
/**
 * Message view block template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
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
							'filepath' => 'message/view/listing',
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
									'filepath' => 'message/view/sender',
									'order'    => 10,
								],

								'message_date'   => [
									'type'     => 'element',
									'filepath' => 'message/view/date',
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
							'filepath' => 'message/view/text',
							'order'    => 10,
						],
					],
				],
			],
		],
	],
];
