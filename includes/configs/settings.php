<?php
/**
 * Settings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'messages' => [
		'title'    => hivepress()->translator->get_string( 'messages' ),
		'_order'   => 110,

		'sections' => [
			'sending' => [
				'title'  => esc_html__( 'Sending', 'hivepress-messages' ),
				'_order' => 10,

				'fields' => [
					'message_allow_attachment' => [
						'label'   => esc_html__( 'Attachments', 'hivepress-messages' ),
						'caption' => esc_html__( 'Allow file attachments', 'hivepress-messages' ),
						'type'    => 'checkbox',
						'_order'  => 10,
					],

					'message_attachment_types' => [
						'label'    => esc_html__( 'Allowed File Types', 'hivepress-messages' ),
						'type'     => 'select',
						'options'  => 'mime_types',
						'multiple' => true,
						'_parent'  => 'message_allow_attachment',
						'_order'   => 20,
					],

					'message_blocked_keywords' => [
						'label'       => esc_html__( 'Blocked Keywords', 'hivepress-messages' ),
						'description' => esc_html__( 'Messages containing these keywords will be blocked, enter each keyword on a new line.', 'hivepress-messages' ),
						'type'        => 'textarea',
						'max_length'  => 2048,
						'_order'      => 30,
					],
				],
			],

			'storage' => [
				'title'  => hivepress()->translator->get_string( 'storage' ),
				'_order' => 20,

				'fields' => [
					'message_enable_storage' => [
						'label'       => hivepress()->translator->get_string( 'storage' ),
						'caption'     => esc_html__( 'Store messages in the database', 'hivepress-messages' ),
						'description' => esc_html__( 'Check this option to store messages in the database, rather than sending them via email.', 'hivepress-messages' ),
						'type'        => 'checkbox',
						'default'     => true,
						'_order'      => 10,
					],

					'message_storage_period' => [
						'label'       => hivepress()->translator->get_string( 'storage_period' ),
						'description' => esc_html__( 'Set the number of days after which a message is deleted.', 'hivepress-messages' ),
						'type'        => 'number',
						'min_value'   => 1,
						'_parent'     => 'message_enable_storage',
						'_order'      => 20,
					],
				],
			],
		],
	],
];
