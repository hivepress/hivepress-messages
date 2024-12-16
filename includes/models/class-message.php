<?php
/**
 * Message model.
 *
 * @package HivePress\Models
 */

namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Message model class.
 *
 * @class Message
 */
class Message extends Comment {

	/**
	 * Class constructor.
	 *
	 * @param array $args Model arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields' => [
					'text'                 => [
						'label'      => esc_html__( 'Message', 'hivepress-messages' ),
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
						'_alias'     => 'comment_content',
					],

					'sent_date'            => [
						'type'   => 'date',
						'format' => 'Y-m-d H:i:s',
						'time'   => true,
						'_alias' => 'comment_date',
					],

					'sender'               => [
						'type'     => 'id',
						'required' => true,
						'_alias'   => 'user_id',
						'_model'   => 'user',
					],

					'sender__display_name' => [
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
						'_alias'     => 'comment_author',
					],

					'sender__email'        => [
						'type'     => 'email',
						'required' => true,
						'_alias'   => 'comment_author_email',
					],

					'recipient'            => [
						'type'     => 'id',
						'required' => true,
						'_alias'   => 'comment_karma',
						'_model'   => 'user',
					],

					'read'                 => [
						'type'      => 'number',
						'min_value' => 0,
						'max_value' => 1,
						'_alias'    => 'comment_approved',
					],

					'listing'              => [
						'type'   => 'id',
						'_alias' => 'comment_post_ID',
						'_model' => 'listing',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Gets user ID.
	 *
	 * @todo Deprecate when attachments are not checked by user.
	 * @return mixed
	 */
	final public function get_user__id() {
		return $this->get_sender__id();
	}
}
