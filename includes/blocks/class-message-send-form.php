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
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'form' => 'message_send',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function boot() {
		if ( is_user_logged_in() ) {

			// Get context.
			$listing = $this->get_context( 'listing' );
			$booking = $this->get_context( 'booking' );
			$vendor  = $this->get_context( 'vendor' );
			$order   = $this->get_context( 'order' );
			$request = $this->get_context( 'request' );
			$user    = $this->get_context( 'user' );

			// Get participant IDs, the first matching context wins.
			if ( $listing ) {
				$user_ids = [ $listing->get_user__id(), $booking ? $booking->get_user__id() : null ];

				$this->values['listing'] = $listing->get_id();
			} elseif ( $vendor ) {
				$user_ids = [ $vendor->get_user__id() ];
			} elseif ( $order ) {
				$user_ids = [ $order->get_buyer__id(), $order->get_seller__id() ];
			} elseif ( $request ) {
				$user_ids = [ $request->get_user__id() ];
			} elseif ( $user ) {
				$user_ids = [ $user->get_id() ];
			} else {
				$user_ids = [ hivepress()->request->get_param( 'user_id' ) ];
			}

			// Set recipient, preferring a participant other than the current user.
			$user_ids = array_filter( array_map( 'absint', $user_ids ) );

			$this->values['recipient'] = hp\get_first_array_value(
				array_diff( $user_ids, [ get_current_user_id() ] ),
				hp\get_first_array_value( $user_ids )
			);

			// Set draft.
			if ( get_option( 'hp_message_allow_attachment' ) ) {
				$this->context['message'] = hivepress()->model->get_draft( 'message' );

				$this->attributes['data-reset'] = 'true';
			}
		}

		parent::boot();
	}
}
