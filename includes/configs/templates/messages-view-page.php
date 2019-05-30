<?php
/**
 * Messages view page template.
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
				'messages'          => [
					'type'  => 'messages',
					'order' => 10,
				],

				'message_send_form' => [
					'type'  => 'message_send_form',
					'order' => 20,
				],
			],
		],
	],
];
