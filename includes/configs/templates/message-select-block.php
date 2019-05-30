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
				'sender'  => [
					'type'      => 'element',
					'filepath' => 'message/select/sender',
					'order'     => 10,
				],

				'listing' => [
					'type'      => 'element',
					'filepath' => 'message/select/listing',
					'order'     => 20,
				],

				'date'    => [
					'type'      => 'element',
					'filepath' => 'message/select/date',
					'order'     => 30,
				],
			],
		],
	],
];
