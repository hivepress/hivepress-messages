<?php
/**
 * Messages block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Messages block class.
 *
 * @class Messages
 */
class Messages extends Block {

	/**
	 * Template mode.
	 *
	 * @var string
	 */
	protected $mode = 'view';

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get messages.
		$messages = $this->get_context( 'messages' );

		if ( $messages ) {

			// Get users.
			$sender    = $this->get_context( 'sender' );
			$recipient = $this->get_context( 'recipient' );

			if ( 'thread' === $this->mode ) {
				$output .= '<table class="hp-messages hp-table">';
			} else {
				$output .= '<div class="hp-messages hp-grid" data-block="' . esc_attr( $this->name ) . '"';

				if ( get_current_user_id() === $recipient->get_id() ) {
					$output .= ' data-render="' . hp\esc_json(
						wp_json_encode(
							[
								'block'    => $this->name,
								'type'     => 'append',
								'interval' => absint( get_option( 'hp_message_refresh_interval', 60 ) ),

								'url'      => hivepress()->router->get_url(
									'messages_read_action',
									[
										'sender'    => $sender->get_id(),
										'recipient' => $recipient->get_id(),
									]
								),
							]
						)
					) . '"';
				}

				$output .= '>';
			}

			foreach ( $messages as $message ) {
				if ( hp\is_class_instance( $message, '\HivePress\Models\Message' ) ) {

					// Get context.
					$sender_name = $message->get_sender__display_name();
					$message_url = null;

					if ( 'thread' === $this->mode ) {
						$message_url_args = [ 'user_id' => $message->get_sender__id() ];

						if ( get_current_user_id() !== $message->get_recipient__id() ) {
							$message_url_args['recipient_id'] = $message->get_recipient__id();

							$sender_name .= '&nbsp;&rarr;&nbsp;' . $message->get_recipient__display_name();
						}

						$message_url = hivepress()->router->get_url( 'messages_view_page', $message_url_args ) . '#message-' . $message->get_id();
					} else {
						$output .= '<div class="hp-grid__item">';
					}

					// Render message.
					$output .= ( new Template(
						[
							'template' => 'message_' . $this->mode . '_block',

							'context'  => [
								'message'     => $message,
								'message_url' => $message_url,
								'sender_name' => $sender_name,
								'recipient'   => $recipient,
							],
						]
					) )->render();

					if ( 'thread' !== $this->mode ) {
						$output .= '</div>';
					}
				}
			}

			if ( 'thread' === $this->mode ) {
				$output .= '</table>';
			} else {
				$output .= '</div>';
			}
		}

		return $output;
	}
}
