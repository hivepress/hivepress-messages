<?php
/**
 * Messages block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

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
			if ( 'thread' === $this->mode ) {
				$output .= '<table class="hp-table">';
			} else {
				$output .= '<div class="hp-grid">';
			}

			foreach ( $messages as $message ) {
				if ( hp\is_class_instance( $message, '\HivePress\Models\Message' ) ) {
					if ( 'thread' !== $this->mode ) {
						$output .= '<div class="hp-grid__item">';
					} elseif ( $message->get_sender__id() === get_current_user_id() ) {

						// Set sender.
						$message->fill(
							[
								'sender'               => get_current_user_id(),
								'sender__display_name' => hivepress()->request->get_user()->get_display_name(),
								'sender__email'        => hivepress()->request->get_user()->get_email(),
							]
						);
					}

					// Render message.
					$output .= ( new Template(
						[
							'template' => 'message_' . $this->mode . '_block',

							'context'  => [
								'message' => $message,
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
