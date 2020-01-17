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
	 * Class constructor.
	 *
	 * @param array $args Email arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'subject' => esc_html__( 'Message Received', 'hivepress-messages' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
