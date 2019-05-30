<?php
/**
 * Listing view block template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'blocks' => [
		'container' => [
			'blocks' => [
				'footer' => [
					'blocks' => [
						'actions_primary' => [
							'blocks' => [
								'message_send_modal' => [
									'type'        => 'modal',
									'model'       => 'listing',
									'caption' => esc_html__( 'Send Message', 'hivepress-messages' ),

									'blocks'      => [
										'message_form' => [
											'type'       => 'message_send_form',
											'order'      => 10,

											'attributes' => [
												'class' => [ 'hp-form--narrow' ],
											],
										],
									],
								],

								'message_link'       => [
									'type'      => 'element',
									'filepath' => 'message/send-link',
									'order'     => 10,
								],
							],
						],
					],
				],
			],
		],
	],
];
