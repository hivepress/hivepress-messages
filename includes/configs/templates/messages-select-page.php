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
		'page_content' => [
			'blocks' => [
				'messages' => [
					'type'     => 'messages',
					'template' => 'select',
					'order'    => 10,
				],
			],
		],
	],
];
