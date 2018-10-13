<?php
namespace HivePress\Messages;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Manages messages.
 *
 * @class Message
 */
class Message extends \HivePress\Component {

	/**
	 * Class constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );

		// Send message.
		add_filter( 'hivepress/form/form_values/message__send', [ $this, 'set_form_values' ] );
		add_action( 'hivepress/form/submit_form/message__send', [ $this, 'send' ] );

		// Delete messages.
		add_action( 'delete_user', [ $this, 'delete' ] );

		if ( ! is_admin() ) {

			// Set template context.
			add_filter( 'hivepress/template/template_context/message_chats', [ $this, 'set_chats_context' ] );
			add_filter( 'hivepress/template/template_context/message_chat', [ $this, 'set_chat_context' ] );

			// Redirect chat.
			add_action( 'hivepress/template/redirect_page/message__chat', [ $this, 'redirect_chat' ] );

			// Set chat title.
			add_filter( 'hivepress/template/page_title/message__chat', [ $this, 'set_chat_title' ] );
		}
	}

	/**
	 * Sends message.
	 *
	 * @param array $values
	 */
	public function send( $values ) {

		// Get recipient.
		$recipient = get_userdata( absint( $values['user_id'] ) );

		if ( false !== $recipient && get_current_user_id() !== $recipient->ID ) {

			// Set arguments.
			$args = [
				'comment_type'         => 'hp_message',
				'user_id'              => get_current_user_id(),
				'comment_author'       => hivepress()->user->get_name(),
				'comment_author_email' => hivepress()->user->get_email(),
				'comment_karma'        => $recipient->ID,
				'comment_post_ID'      => absint( $values['post_id'] ),
				'comment_content'      => $values['message'],
			];

			if ( wp_insert_comment( $args ) !== false ) {

				// Send email.
				hivepress()->email->send(
					'message__send',
					[
						'to'           => $recipient->user_email,
						'placeholders' => [
							'user_name'    => $recipient->display_name,
							'message_url'  => hivepress()->template->get_url( 'message__chat', [ get_current_user_id() ] ),
							'message_text' => $values['message'],
						],
					]
				);
			}
		}
	}

	/**
	 * Deletes messages.
	 *
	 * @param int $user_id
	 */
	public function delete( $user_id ) {

		// Get message IDs.
		$args = [
			'type'   => 'hp_message',
			'fields' => 'ids',
		];

		$message_ids = array_merge(
			get_comments(
				array_merge(
					$args,
					[
						'user_id' => $user_id,
					]
				)
			),
			get_comments(
				array_merge(
					$args,
					[
						'karma' => $user_id,
					]
				)
			)
		);

		// Delete messages.
		foreach ( $message_ids as $message_id ) {
			wp_delete_comment( $message_id, true );
		}
	}

	/**
	 * Sets form values.
	 *
	 * @param array $values
	 * @return array
	 */
	public function set_form_values( $values ) {
		if ( get_query_var( 'hp_listing_vendor' ) ) {
			$vendor = get_user_by( 'login', sanitize_user( get_query_var( 'hp_listing_vendor' ) ) );

			if ( false !== $vendor ) {
				$values['user_id'] = $vendor->ID;
			}
		} elseif ( get_query_var( 'hp_message_chat' ) ) {
			$values['user_id'] = absint( get_query_var( 'hp_message_chat' ) );
		} else {
			$values['user_id'] = get_the_author_meta( 'ID' );
			$values['post_id'] = get_the_ID();
		}

		return $values;
	}

	/**
	 * Sets chats context.
	 *
	 * @param array $context
	 * @return array
	 */
	public function set_chats_context( $context ) {

		// Set default arguments.
		$args = [
			'type'   => 'hp_message',
			'status' => 'approve',
			'order'  => 'ASC',
		];

		// Get all messages.
		$messages = wp_list_sort(
			array_merge(
				get_comments(
					array_merge(
						$args,
						[
							'user_id' => get_current_user_id(),
						]
					)
				),
				get_comments(
					array_merge(
						$args,
						[
							'karma' => get_current_user_id(),
						]
					)
				)
			),
			'comment_date',
			'DESC'
		);

		// Get chats.
		$chats = [];

		foreach ( $messages as $message ) {
			if ( $message->user_id !== $message->comment_karma ) {

				// Set sender.
				if ( get_current_user_id() === absint( $message->user_id ) ) {
					$recipient = get_userdata( $message->comment_karma );

					if ( false !== $recipient ) {
						$message->user_id        = $message->comment_karma;
						$message->comment_author = $recipient->display_name;
					} else {
						continue;
					}
				}

				// Add chat.
				if ( ! empty( $message->user_id ) ) {
					if ( ! isset( $chats[ $message->user_id ] ) ) {
						$chats[ $message->user_id ] = $message;
					} elseif ( empty( $chats[ $message->user_id ]->comment_post_ID ) && ! empty( $message->comment_post_ID ) ) {
						$chats[ $message->user_id ]->comment_post_ID = $message->comment_post_ID;
					}
				}
			}
		}

		$context['chats'] = $chats;

		return $context;
	}

	/**
	 * Sets chat context.
	 *
	 * @param array $context
	 * @return array
	 */
	public function set_chat_context( $context ) {

		// Set default arguments.
		$args = [
			'type'   => 'hp_message',
			'status' => 'approve',
			'order'  => 'ASC',
		];

		// Get recipient ID.
		$recipient_id = absint( get_query_var( 'hp_message_chat' ) );

		// Get messages.
		$messages = [];

		if ( get_current_user_id() !== $recipient_id ) {
			$messages = wp_list_sort(
				array_merge(
					get_comments(
						array_merge(
							$args,
							[
								'user_id' => get_current_user_id(),
								'karma'   => $recipient_id,
							]
						)
					),
					get_comments(
						array_merge(
							$args,
							[
								'user_id' => $recipient_id,
								'karma'   => get_current_user_id(),
							]
						)
					)
				),
				'comment_date'
			);
		}

		$context['messages'] = $messages;

		return $context;
	}

	/**
	 * Redirects chat.
	 */
	public function redirect_chat() {

		// Get user ID.
		$user_id = absint( get_query_var( 'hp_message_chat' ) );

		// Redirect user.
		if ( get_current_user_id() === $user_id || get_userdata( $user_id ) === false ) {
			hp_redirect( hivepress()->template->get_url( 'message__chats' ) );
		}
	}

	/**
	 * Sets chat title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function set_chat_title( $title ) {
		$user = get_userdata( absint( get_query_var( 'hp_message_chat' ) ) );

		if ( false !== $user ) {
			$title = sprintf( esc_html__( 'Chat with %s', 'hivepress-messages' ), $user->display_name );
		}

		return $title;
	}
}
