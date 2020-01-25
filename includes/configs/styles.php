<?php
/**
 * Styles configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'messages_frontend' => [
		'handle'  => 'hivepress-messages-frontend',
		'src'     => hivepress()->get_url( 'messages' ) . '/assets/css/frontend.min.css',
		'version' => hivepress()->get_version( 'messages' ),
	],
];
