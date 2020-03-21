<?php
/**
 * Message component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message component class.
 *
 * @class Message
 */
final class Message extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Expire messages.
		add_action( 'hivepress/v1/events/hourly', [ $this, 'expire_messages' ] );

		// Delete messages.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_messages' ] );

		if ( ! is_admin() ) {

			// Alter account menu.
			add_filter( 'hivepress/v1/menus/user_account', [ $this, 'alter_account_menu' ] );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_block' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_block', [ $this, 'alter_vendor_view_block' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_page' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Expires messages.
	 */
	public function expire_messages() {
		Models\Message::query()->filter(
			[
				'expired_time__lte' => time(),
			]
		)->delete();
	}

	/**
	 * Deletes messages.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_messages( $user_id ) {
		Models\Message::query()->filter(
			[
				'sender' => $user_id,
			]
		)->delete();

		Models\Message::query()->filter(
			[
				'recipient' => $user_id,
			]
		)->delete();
	}

	/**
	 * Alters account menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_account_menu( $menu ) {
		if ( get_option( 'hp_message_enable_storage' ) && ( Models\Message::query()->filter(
			[
				'sender' => get_current_user_id(),
			]
		)->get_first_id() || Models\Message::query()->filter(
			[
				'recipient' => get_current_user_id(),
			]
		)->get_first_id() ) ) {
			$menu['items']['messages_thread'] = [
				'route'  => 'messages_thread_page',
				'_order' => 30,
			];
		}

		return $menu;
	}

	/**
	 * Alters listing view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'listing_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'   => 'modal',
								'model'  => 'listing',
								'title'  => hivepress()->translator->get_string( 'reply_to_listing' ),

								'blocks' => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'_order'     => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'listing/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters listing view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'listing_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'   => 'modal',
								'model'  => 'listing',
								'title'  => hivepress()->translator->get_string( 'reply_to_listing' ),

								'blocks' => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'_order'     => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'listing/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'   => 'modal',
								'model'  => 'vendor',
								'title'  => esc_html__( 'Send Message', 'hivepress-messages' ),

								'blocks' => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'_order'     => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'vendor/view/block/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_actions_primary' => [
						'blocks' => [
							'message_send_modal' => [
								'type'   => 'modal',
								'model'  => 'vendor',
								'title'  => esc_html__( 'Send Message', 'hivepress-messages' ),

								'blocks' => [
									'message_send_form' => [
										'type'       => 'message_send_form',
										'_order'     => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'message_send_link'  => [
								'type'   => 'part',
								'path'   => 'vendor/view/page/message-send-link',
								'_order' => 10,
							],
						],
					],
				],
			]
		);
	}
}
