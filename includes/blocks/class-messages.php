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
				$output .= '<table class="hp-messages hp-table">';
			} else {
				$output .= '<div class="hp-messages hp-grid">';
			}

			foreach ( $messages as $message ) {
				if ( hp\is_class_instance( $message, '\HivePress\Models\Message' ) ) {
					if ( 'thread' !== $this->mode ) {
						$output .= '<div class="hp-grid__item">';
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
