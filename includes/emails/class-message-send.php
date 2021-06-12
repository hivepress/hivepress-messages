<?php
/**
 * Message send email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message send email class.
 *
 * @class Message_Send
 */
class Message_Send extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Message Received', 'hivepress-messages' ),
				'description' => esc_html__( 'This email is sent to users when a new message is received.', 'hivepress-messages' ),
				'recipient'   => hivepress()->translator->get_string( 'user' ),
				'tokens'      => [ 'user_name', 'message_url', 'message_text', 'message', 'sender', 'recipient' ],
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Email arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'subject' => hp\sanitize_html( __( 'Message Received', 'hivepress-messages' ) ),
				'body'    => hp\sanitize_html( __( "Hi, %user_name%! You've received a new message, click on the following link to view it: %message_url%", 'hivepress-messages' ) ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
