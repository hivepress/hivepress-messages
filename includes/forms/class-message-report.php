<?php
/**
 * Message report form.
 *
 * @package HivePress\Forms
 */

namespace HivePress\Forms;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Reports message.
 */
class Message_Report extends Model_Form {

    /**
     * Class initializer.
     *
     * @param array $meta Class meta values.
     */
    public static function init( $meta = [] ) {
        $meta = hp\merge_arrays(
            [
                'label'   => esc_html__( 'Report User', 'hivepress-messages' ),
                'model'   => 'message',
                'captcha' => false,
            ],
            $meta
        );

        parent::init( $meta );
    }

    /**
     * Class constructor.
     *
     * @param array $args Form arguments.
     */
    public function __construct( $args = [] ) {
        $args = hp\merge_arrays(
            [
                'description' => esc_html__( 'Please provide details that will help us verify that this user violates the terms of service.', 'hivepress-messages' ),
                'message'     => esc_html__( 'User have been reported.', 'hivepress-messages' ),
                'reset'       => true,

                'fields'      => [
                    'details' => [
                        'label'      => hivepress()->translator->get_string( 'details' ),
                        'type'       => 'textarea',
                        'max_length' => 2048,
                        'required'   => true,
                        '_separate'  => true,
                        '_order'     => 10,
                    ],
                ],

                'button'      => [
                    'label' => esc_html__( 'Report User', 'hivepress-messages' ),
                ],
            ],
            $args
        );

        parent::__construct( $args );
    }

    /**
     * Bootstraps form properties.
     */
    protected function boot() {

        // Get sender.
        $sender = hivepress()->request->get_context( 'message_sender' );

        // Set action.
        if ( $sender ) {
            $this->action = hivepress()->router->get_url(
                'message_report_action',
                [
                    'user_id' => $sender->get_id(),
                ]
            );
        }

        parent::boot();
    }
}
