<?php
/**
 * Message report email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Sent to admins when a message is reported.
 */
class Message_Report extends Email {

    /**
     * Class constructor.
     *
     * @param array $args Email arguments.
     */
    public function __construct( $args = [] ) {
        $args = hp\merge_arrays(
            [
                'subject' => esc_html__( 'User Reported', 'hivepress-messages' ),
                'body'    => hp\sanitize_html( __( 'User "%user_name%" %user_url% has been reported with the following details: %report_details%', 'hivepress-messages' ) ),
            ],
            $args
        );

        parent::__construct( $args );
    }
}
