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
	'users' => [
		'sections' => [
			'emails' => [
				'fields' => [
					'email_message_send' => [
						'label'       => esc_html__( 'Message Received', 'hivepress-messages' ),
						'description' => esc_html__( 'This email is sent to users when a new message is received, the following tokens are available: %user_name%, %message_url%, %message_text%.', 'hivepress-messages' ),
						'type'        => 'textarea',
						'default'     => hp\sanitize_html( __( "Hi, %user_name%! You've received a new message, click on the following link to view it: %message_url%", 'hivepress-messages' ) ),
						'html'        => 'post',
						'required'    => true,
						'autoload'    => false,
						'order'       => 30,
					],
				],
			],
		],
	],
];
