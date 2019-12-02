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
	 * Email subject.
	 *
	 * @var string
	 */
	protected static $subject;

	/**
	 * Email body.
	 *
	 * @var string
	 */
	protected static $body;

	/**
	 * Class initializer.
	 *
	 * @param array $args Email arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'subject' => esc_html__( 'Message Received', 'hivepress-messages' ),
				'body'    => hp\sanitize_html( __( "Hi, %user_name%! You've received a new message, click on the following link to view it: %message_url%", 'hivepress-messages' ) ),
			],
			$args
		);

		parent::init( $args );
	}
}
