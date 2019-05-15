<?php
/**
 * Message select block template.
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
			'tag'        => 'tr',
			'order'      => 10,

			'attributes' => [
				'class' => [ 'hp-message', 'hp-message--select-block' ],
			],

			'blocks'     => [
				'sender' => [
					'type'      => 'element',
					'file_path' => 'message/select/sender',
					'order'     => 10,
				],

				'date'   => [
					'type'      => 'element',
					'file_path' => 'message/select/date',
					'order'     => 20,
				],
			],
		],
	],
];
