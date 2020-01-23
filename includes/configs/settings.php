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
		'title'    => esc_html__( 'Messages', 'hivepress-messages' ),
		'_order'   => 110,

		'sections' => [
			'expiration' => [
				'title'  => hivepress()->translator->get_string( 'expiration' ),
				'_order' => 10,

				'fields' => [
					'message_expiration_period' => [
						'label'       => esc_html__( 'Expiration Period', 'hivepress-messages' ),
						'description' => esc_html__( 'Set the number of days after which a message is deleted.', 'hivepress-messages' ),
						'type'        => 'number',
						'min_value'   => 1,
						'_order'      => 10,
					],
				],
			],

			'emails'     => [
				'title'  => hivepress()->translator->get_string( 'emails' ),
				'_order' => 1000,

				'fields' => [
					'email_message_send' => [
						'label'       => esc_html__( 'Message Received', 'hivepress-messages' ),
						'description' => esc_html__( 'This email is sent to users when a new message is received.', 'hivepress-messages' ) . ' ' . sprintf( hivepress()->translator->get_string( 'these_tokens_are_available' ), '%user_name%, %message_url%, %message_text%' ),
						'type'        => 'textarea',
						'default'     => hp\sanitize_html( __( "Hi, %user_name%! You've received a new message, click on the following link to view it: %message_url%", 'hivepress-messages' ) ),
						'max_length'  => 2048,
						'html'        => true,
						'_autoload'   => false,
						'_order'      => 10,
					],
				],
			],
		],
	],
];
