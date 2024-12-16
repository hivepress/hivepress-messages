<?php
/**
 * Messages view page template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Messages view page template class.
 *
 * @class Messages_View_Page
 */
class Messages_View_Page extends User_Account_Page {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Class meta values.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => esc_html__( 'Conversation', 'hivepress-messages' ),
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'page_content' => [
						'blocks' => [
							'messages'          => [
								'type'   => 'messages',
								'_label' => hivepress()->translator->get_string( 'messages' ),
								'_order' => 10,
							],

							'message_send_form' => [
								'type'       => 'message_send_form',
								'message'    => '',
								'_label'     => hivepress()->translator->get_string( 'form' ),
								'_order'     => 20,

								'attributes' => [
									'data-render' => wp_json_encode(
										[
											'block' => 'messages',
											'event' => 'submit',
											'type'  => 'append',
										]
									),
								],
							],
						],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
