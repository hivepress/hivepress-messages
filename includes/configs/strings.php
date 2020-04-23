<?php
/**
 * Strings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'messages'         => esc_html__( 'Messages', 'hivepress-messages' ),
	'reply_to_listing' => esc_html__( 'Reply to Listing', 'hivepress-messages' ),
];
