<?php
/**
 * Messages view page template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Messages view page template class.
 *
 * @class Messages_View_Page
 */
class Messages_View_Page extends User_Account_Page {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Class meta values.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => esc_html__( 'Conversation', 'hivepress-messages' ),
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'page_content' => [
						'blocks' => [
							'messages'          => [
								'type'   => 'messages',
								'_label' => hivepress()->translator->get_string( 'messages' ),
								'_order' => 10,
							],

							'message_send_form' => [
								'type'   => 'message_send_form',
								'_label' => hivepress()->translator->get_string( 'form' ),
								'_order' => 20,

                                'footer' => [
                                    'form_actions' => [
                                        'type'       => 'container',
                                        '_order'     => 10,

                                        'attributes' => [
                                            'class' => [ 'hp-form__actions' ],
                                        ],

                                        'blocks'     => [
                                            'message_report_link' => [
                                                'type'   => 'part',
                                                'path'   => 'message/view/message-report-link',
                                                '_order' => 10,
                                            ],
                                        ],
                                    ],
                                ],
							],

                            'message_report_modal' => [
                                'type'        => 'modal',
                                'title'       => esc_html__( 'Report User', 'hivepress-messages' ),
                                '_capability' => 'read',

                                'blocks'      => [
                                    'message_report_form' => [
                                        'type'   => 'form',
                                        'form'   => 'message_report',
                                        '_order' => 10,
                                    ],
                                ],
                            ],
						],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
