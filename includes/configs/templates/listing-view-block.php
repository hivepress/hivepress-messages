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
		'listing_container' => [
			'blocks' => [
				'listing_footer' => [
					'blocks' => [
						'listing_actions_primary' => [
							'blocks' => [
								'message_send_modal' => [
									'type'    => 'modal',
									'model'   => 'listing',
									'caption' => esc_html__( 'Send Message', 'hivepress-messages' ),

									'blocks'  => [
										'message_send_form' => [
											'type'       => 'message_send_form',
											'order'      => 10,

											'attributes' => [
												'class' => [ 'hp-form--narrow' ],
											],
										],
									],
								],

								'message_send_link'  => [
									'type'     => 'element',
									'filepath' => 'message/send/send-link',
									'order'    => 10,
								],
							],
						],
					],
				],
			],
		],
	],
];
