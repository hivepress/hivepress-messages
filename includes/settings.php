<?php
/**
 * Contains plugin settings.
 *
 * @package HivePress\Messages
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = [

	// Message component.
	'message' => [

		// Options.
		'options'   => [
			'users' => [
				'sections' => [
					'emails' => [
						'fields' => [
							'email_message_send' => [
								'name'        => esc_html__( 'Message Received', 'hivepress-messages' ),
								'description' => esc_html__( 'This email is sent to users when new message is received, the following placeholders are available: %user_name%, %message_url%, %message_text%.', 'hivepress-messages' ),
								'type'        => 'textarea',
								'default'     => hp_sanitize_html( __( 'Hi, %user_name%! You received a new message, click on the following link to view it: %message_url%', 'hivepress-messages' ) ),
								'required'    => true,
								'order'       => 30,
							],
						],
					],
				],
			],
		],

		// Emails.
		'emails'    => [
			'send' => [
				'subject' => esc_html__( 'Message Received', 'hivepress-messages' ),
			],
		],

		// Forms.
		'forms'     => [
			'send' => [
				'name'            => esc_html__( 'Send Message', 'hivepress-messages' ),
				'capability'      => 'read',
				'captcha'         => false,
				'success_message' => esc_html__( 'Your message has been sent.', 'hivepress-messages' ),

				'fields'          => [
					'message' => [
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
						'order'      => 10,
					],

					'user_id' => [
						'type' => 'hidden',
					],

					'post_id' => [
						'type' => 'hidden',
					],
				],

				'submit_button'   => [
					'name' => esc_html__( 'Send Message', 'hivepress-messages' ),
				],
			],
		],

		// Pages.
		'pages'     => [
			'chats' => [
				'title'      => esc_html__( 'My Messages', 'hivepress-messages' ),
				'regex'      => '^account/chats/?$',
				'redirect'   => 'index.php?hp_message_chats=1',
				'capability' => 'read',
				'template'   => 'message_chats',
				'menu'       => 'user_account',
				'order'      => 30,
			],

			'chat'  => [
				'regex'      => '^account/chat/([0-9]+)/?$',
				'redirect'   => 'index.php?hp_message_chat=$matches[1]',
				'capability' => 'read',
				'template'   => 'message_chat',
			],
		],

		// Templates.
		'templates' => [
			'message_chats'   => [
				'parent' => 'user_account',

				'areas'  => [
					'content' => [
						'loop' => [
							'path'  => 'message/parts/loop-chat',
							'order' => 20,
						],
					],
				],
			],

			'message_chat'    => [
				'parent' => 'user_account',

				'areas'  => [
					'content' => [
						'loop'      => [
							'path'  => 'message/parts/loop-message',
							'order' => 20,
						],

						'send_form' => [
							'path'  => 'message/parts/send-form',
							'order' => 30,
						],
					],
				],
			],

			'archive_listing' => [
				'areas' => [
					'actions' => [
						'message_link' => [
							'path'  => 'listing/parts/message-link',
							'order' => 10,
						],
					],
				],
			],

			'single_listing'  => [
				'areas' => [
					'actions' => [
						'message_button' => [
							'path'  => 'listing/parts/message-button',
							'order' => 10,
						],
					],
				],
			],

			'archive_vendor'  => [
				'areas' => [
					'actions' => [
						'message_link' => [
							'path'  => 'vendor/parts/message-link',
							'order' => 10,
						],
					],
				],
			],

			'single_vendor'   => [
				'areas' => [
					'actions' => [
						'message_button' => [
							'path'  => 'vendor/parts/message-button',
							'order' => 10,
						],
					],
				],
			],
		],

		// Styles.
		'styles'    => [
			'frontend' => [
				'handle'  => 'hp-messages',
				'src'     => HP_MESSAGES_URL . '/assets/css/frontend.min.css',
				'version' => HP_MESSAGES_VERSION,
			],
		],
	],
];
