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
	 * Template name.
	 *
	 * @var string
	 */
	protected $template_name;

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get messages.
		$messages = [];

		if ( 'message_select_block' === $this->template_name ) {
			$all_messages = wp_list_sort(
				array_merge(
					get_comments(
						[
							'type'    => 'hp_message',
							'user_id' => get_current_user_id(),
						]
					),
					get_comments(
						[
							'type'  => 'hp_message',
							'karma' => get_current_user_id(),
						]
					)
				),
				'comment_date',
				'DESC'
			);

			foreach ( $all_messages as $message ) {

				// Get user ID.
				$user_id = absint( $message->user_id );

				if ( get_current_user_id() === $user_id ) {
					$user_id = absint( $message->comment_karma );
				}

				// Add message.
				if ( ! isset( $messages[ $user_id ] ) ) {
					$messages[ $user_id ] = $message;
				}
			}
		} else {

			// Get recipient ID.
			$recipient_id = absint( get_query_var( 'hp_user_id' ) );

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
		}

		// Render messages.
		if ( ! empty( $messages ) ) {
			if ( 'message_select_block' === $this->template_name ) {
				$output = '<table class="hp-table">';

				foreach ( $messages as $message ) {
					$output .= ( new Message(
						[
							'template_name' => $this->template_name,
							'id'            => absint( $message->comment_ID ),
						]
					) )->render();
				}

				$output .= '</table>';
			} else {
				$output = '<div class="hp-grid">';

				foreach ( $messages as $message ) {
					$output .= '<div class="hp-grid__item">';

					$output .= ( new Message(
						[
							'template_name' => $this->template_name,
							'id'            => absint( $message->comment_ID ),
						]
					) )->render();

					$output .= '</div>';
				}

				$output .= '</div>';
			}
		}

		return $output;
	}
}
