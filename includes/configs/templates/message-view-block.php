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
		'container' => [
			'type'       => 'container',
			'order'      => 10,

			'attributes' => [
				'class' => [ 'hp-message', 'hp-message--view-block' ],
			],

			'blocks'     => [
				'header'  => [
					'type'       => 'container',
					'tag'        => 'header',
					'order'      => 10,

					'attributes' => [
						'class' => [ 'hp-message__header' ],
					],

					'blocks'     => [
						'listing' => [
							'type'      => 'element',
							'filepath' => 'message/view/listing',
							'order'     => 10,
						],

						'details' => [
							'type'       => 'container',
							'order'      => 20,

							'attributes' => [
								'class' => [ 'hp-message__details' ],
							],

							'blocks'     => [
								'sender' => [
									'type'      => 'element',
									'filepath' => 'message/view/sender',
									'order'     => 10,
								],

								'date'   => [
									'type'      => 'element',
									'filepath' => 'message/view/date',
									'order'     => 20,
								],
							],
						],
					],
				],

				'content' => [
					'type'       => 'container',
					'order'      => 20,

					'attributes' => [
						'class' => [ 'hp-message__content' ],
					],

					'blocks'     => [
						'text' => [
							'type'      => 'element',
							'filepath' => 'message/view/text',
							'order'     => 10,
						],
					],
				],
			],
		],
	],
];
