<?php
/**
 * Message block toggle block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message block toggle block class.
 *
 * @class Message_Block_Toggle
 */
class Message_Block_Toggle extends Toggle {

    /**
     * Class constructor.
     *
     * @param array $args Block arguments.
     */
    public function __construct( $args = [] ) {
        $args = hp\merge_arrays(
            [
                'states' => [
                    [
                        'icon'    => 'plus',
                        'caption' => esc_html__( 'Block user', 'hivepress-messages' ),
                    ],
                    [
                        'icon'    => 'minus',
                        'caption' => esc_html__( 'Unblock user', 'hivepress-messages' ),
                    ],
                ],
            ],
            $args
        );

        parent::__construct( $args );
    }

    /**
     * Bootstraps block properties.
     */
    protected function boot() {

        // Get sender.
        $sender = hivepress()->request->get_context( 'message_sender' );

        if ( $sender ) {

            // Set URL for sending requests on click.
            $this->url = hivepress()->router->get_url( 'messages_block_user', [ 'user_id' => $sender->get_id() ] );

            // Set active state if user is blocked.
            if ( in_array( $sender->get_id(), (array) get_user_meta( get_current_user_id(), 'hp_blocked_users', true ) ) ) {
                $this->active = true;
            }
        }

        parent::boot();
    }
}
