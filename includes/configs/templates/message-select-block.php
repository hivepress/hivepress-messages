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
		'message_container' => [
			'type'       => 'container',
			'tag'        => 'tr',
			'order'      => 10,

			'attributes' => [
				'class' => [ 'hp-message', 'hp-message--select-block' ],
			],

			'blocks'     => [
				'message_sender'  => [
					'type'     => 'element',
					'filepath' => 'message/select/sender',
					'order'    => 10,
				],

				'message_listing' => [
					'type'     => 'element',
					'filepath' => 'message/select/listing',
					'order'    => 20,
				],

				'message_date'    => [
					'type'     => 'element',
					'filepath' => 'message/select/date',
					'order'    => 30,
				],
			],
		],
	],
];
