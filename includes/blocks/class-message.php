<?php
/**
 * Message block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message block class.
 *
 * @class Message
 */
class Message extends Template {

	/**
	 * Message ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		if ( isset( $this->id ) ) {

			// Get message.
			$message = \HivePress\Models\Message::get( $this->id );

			if ( ! is_null( $message ) ) {

				// Set sender.
				if ( 'message_select_block' === $this->template_name && $message->get_sender_id() === get_current_user_id() ) {
					$message->set_sender_id( $message->get_recipient_id() );
					$message->set_sender_name( get_userdata( $message->get_recipient_id() )->display_name );
				}

				$this->set_message( $message );

				// Render message.
				$output = parent::render();
			}
		}

		return $output;
	}
}
