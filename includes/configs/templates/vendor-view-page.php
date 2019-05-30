<?php
/**
 * Vendor view page template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'blocks' => [
		'page_content' => [
			'blocks' => [
				'columns' => [
					'blocks' => [
						'sidebar' => [
							'blocks' => [
								'actions_primary' => [
									'blocks' => [
										'message_send_modal' => [
											'type'        => 'modal',
											'caption' => esc_html__( 'Send Message', 'hivepress-messages' ),

											'blocks'      => [
												'message_form' => [
													'type' => 'message_send_form',
													'order' => 10,

													'attributes' => [
														'class' => [ 'hp-form--narrow' ],
													],
												],
											],
										],

										'message_button' => [
											'type'      => 'element',
											'filepath' => 'message/send-button',
											'order'     => 10,
										],
									],
								],
							],
						],
					],
				],
			],
		],
	],
];
