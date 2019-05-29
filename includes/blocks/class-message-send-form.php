<?php
/**
 * Message send form block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message send form block class.
 *
 * @class Message_Send_Form
 */
class Message_Send_Form extends Form {

	/**
	 * Block type.
	 *
	 * @var string
	 */
	protected static $type;

	/**
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'form_name' => 'message_send',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function bootstrap() {

		// Set values.
		if ( in_array( get_post_type(), [ 'hp_vendor', 'hp_listing' ], true ) ) {
			$this->values['recipient_id'] = get_the_author_meta( 'ID' );

			if ( get_post_type() === 'hp_listing' ) {
				$this->values['listing_id'] = get_the_ID();
			}
		} else {
			$this->values['recipient_id'] = get_query_var( 'hp_user_id' );
		}

		parent::bootstrap();
	}
}
