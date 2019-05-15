<?php
/**
 * Message block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message block class.
 *
 * @class Message
 */
class Message extends Template {

	/**
	 * Message ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		if ( 0 !== $this->id ) {

			// Get message.
			$message = \HivePress\Models\Message::get( $this->id );

			if ( ! is_null( $message ) ) {
				$this->set_message( $message );

				// Render message.
				$output = parent::render();
			}
		}

		return $output;
	}
}