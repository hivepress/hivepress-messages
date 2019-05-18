<?php
/**
 * Listing view block template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'blocks' => [
		'container' => [
			'blocks' => [
				'footer' => [
					'blocks' => [
						'actions_primary' => [
							'blocks' => [
								'message_link' => [
									'type'      => 'element',
									'file_path' => 'message/send-link',
									'order'     => 10,
								],
							],
						],
					],
				],
			],
		],
	],
];
