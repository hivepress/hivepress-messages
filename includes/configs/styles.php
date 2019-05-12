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
		'handle' => 'hp-messages-frontend',
		'src'    => HP_MESSAGES_URL . '/assets/css/frontend.min.css',
		'editor' => true,
	],
];
