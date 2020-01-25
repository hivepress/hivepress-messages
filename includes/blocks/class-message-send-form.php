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

		// Set recipient.
		$this->values['recipient'] = hivepress()->request->get_param( 'user_id' );

		// Get listing.
		$listing = $this->get_context( 'listing' );

		if ( hp\is_class_instance( $listing, '\HivePress\Models\Listing' ) ) {
			$this->values = array_merge(
				$this->values,
				[
					'recipient' => $listing->get_user__id(),
					'listing'   => $listing->get_id(),
				]
			);
		} else {

			// Get vendor.
			$vendor = $this->get_context( 'vendor' );

			if ( hp\is_class_instance( $vendor, '\HivePress\Models\Vendor' ) ) {
				$this->values['recipient'] = $vendor->get_user__id();
			}
		}

		parent::boot();
	}
}
