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

			// Set recipient.
			$this->values['recipient'] = hivepress()->request->get_param( 'user_id' );

			// Get listing.
			$listing = $this->get_context( 'listing' );

			if ( $listing ) {
				$this->values = array_merge(
					$this->values,
					[
						'recipient' => $listing->get_user__id(),
						'listing'   => $listing->get_id(),
					]
				);

				// Get booking.
				$booking = $this->get_context( 'booking' );

				if ( $booking && get_current_user_id() === $listing->get_user__id() ) {
					$this->values['recipient'] = $booking->get_user__id();
				}
			} else {

				// Get vendor.
				$vendor = $this->get_context( 'vendor' );

				if ( $vendor ) {
					$this->values['recipient'] = $vendor->get_user__id();
				} elseif ( hivepress()->get_version( 'marketplace' ) || hivepress()->get_version( 'requests' ) ) {

					// Get order.
					$order = $this->get_context( 'order' );

					if ( $order ) {
						if ( get_current_user_id() === $order->get_buyer__id() ) {
							$this->values['recipient'] = $order->get_seller__id();
						} else {
							$this->values['recipient'] = $order->get_buyer__id();
						}
					} else {

						// Get request.
						$request = $this->get_context( 'request' );

						if ( $request ) {
							$this->values['recipient'] = $request->get_user__id();
						} else {

							// Get user.
							$user = $this->get_context( 'user' );

							if ( $user ) {
								$this->values['recipient'] = $user->get_id();
							}
						}
					}
				}
			}

			// Set draft.
			if ( get_option( 'hp_message_allow_attachment' ) ) {
				$this->context['message'] = hivepress()->message->get_message_draft();

				$this->attributes['data-reset'] = 'true';
			}
		}

		parent::boot();
	}
}
