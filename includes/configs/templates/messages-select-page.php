<?php
/**
 * Messages select page template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'parent' => 'account_page',

	'blocks' => [
		'content' => [
			'blocks' => [
				'messages' => [
					'type'          => 'messages',
					'template_name' => 'message_select_block',
					'order'         => 10,
				],
			],
		],
	],
];
