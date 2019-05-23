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
	 * Template context.
	 *
	 * @var string
	 */
	protected $template_context = 'view';

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get messages.
		$messages = [];

		if ( 'select' === $this->template_context ) {
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

				if ( ! isset( $messages[ $user_id ] ) ) {

					// Add message.
					$messages[ $user_id ] = $message;
				} elseif ( empty( $messages[ $user_id ]->comment_post_ID ) && ! empty( $message->comment_post_ID ) ) {

					// Set listing ID.
					$messages[ $user_id ]->comment_post_ID = $message->comment_post_ID;
				}
			}
		} else {
			$messages = wp_list_sort(
				array_merge(
					get_comments(
						[
							'type'    => 'hp_message',
							'user_id' => get_current_user_id(),
							'karma'   => absint( get_query_var( 'hp_user_id' ) ),
						]
					),
					get_comments(
						[
							'type'    => 'hp_message',
							'user_id' => absint( get_query_var( 'hp_user_id' ) ),
							'karma'   => get_current_user_id(),
						]
					)
				),
				'comment_date'
			);
		}

		// Render messages.
		if ( ! empty( $messages ) ) {
			if ( 'select' === $this->template_context ) {
				$output .= '<table class="hp-table">';
			} else {
				$output .= '<div class="hp-grid">';
			}

			foreach ( $messages as $message_args ) {

				// Get message.
				$message = Models\Message::get( $message_args->comment_ID );

				if ( ! is_null( $message ) ) {
					if ( 'select' === $this->template_context ) {

						// Set sender.
						if ( $message->get_sender_id() === get_current_user_id() ) {
							$message->set_sender_id( $message->get_recipient_id() );
							$message->set_sender_name( get_userdata( $message->get_recipient_id() )->display_name );
						}

						// Set listing ID.
						if ( ! empty( $message_args->comment_post_ID ) ) {
							$message->set_listing_id( $message_args->comment_post_ID );
						}
					} else {
						$output .= '<div class="hp-grid__item">';
					}

					// Render message.
					$output .= ( new Template(
						[
							'template_name' => 'message_' . $this->template_context . '_block',
							'message'       => $message,
						]
					) )->render();

					if ( 'select' !== $this->template_context ) {
						$output .= '</div>';
					}
				}
			}

			if ( 'select' === $this->template_context ) {
				$output .= '</table>';
			} else {
				$output .= '</div>';
			}
		}

		return $output;
	}
}
