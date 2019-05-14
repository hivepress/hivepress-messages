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
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get messages.
		// todo.
		$messages = get_comments( [ 'type' => 'hp_message' ] );

		// Render messages.
		if ( ! empty( $messages ) ) {
			$output = '<div class="hp-todo">';

			foreach ( $messages as $message ) {
				$output .= '<div class="hp-todo__item">';

				$output .= ( new Message(
					[
						'template_name' => 'message_view_block',
						'id'            => absint( $message->comment_ID ),
					]
				) )->render();

				$output .= '</div>';
			}

			$output .= '</div>';
		}

		return $output;
	}
}
