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

		// Get recipient ID.
		$recipient_id = absint( get_query_var( 'hp_recipient_id' ) );

		// Get messages.
		$messages = wp_list_sort(
			array_merge(
				get_comments(
					[
						'type'    => 'hp_message',
						'user_id' => get_current_user_id(),
						'karma'   => $recipient_id,
					]
				),
				get_comments(
					[
						'type'    => 'hp_message',
						'user_id' => $recipient_id,
						'karma'   => get_current_user_id(),
					]
				)
			),
			'comment_date'
		);

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
