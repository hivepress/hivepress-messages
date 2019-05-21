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
	'messages_backend'  => [
		'handle'  => 'hp-messages-backend',
		'src'     => HP_MESSAGES_URL . '/assets/css/backend.min.css',
		'version' => HP_MESSAGES_VERSION,
		'admin'   => true,
	],

	'messages_frontend' => [
		'handle'  => 'hp-messages-frontend',
		'src'     => HP_MESSAGES_URL . '/assets/css/frontend.min.css',
		'version' => HP_MESSAGES_VERSION,
		'editor'  => true,
	],
];
