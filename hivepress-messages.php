<?php
/**
 * Plugin Name: HivePress Messages
 * Description: Allow users to send private messages.
 * Version: 1.1.3
 * Author: HivePress
 * Author URI: https://hivepress.io/
 * Text Domain: hivepress-messages
 * Domain Path: /languages/
 *
 * @package HivePress
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register plugin directory.
add_filter(
	'hivepress/v1/dirs',
	function( $dirs ) {
		return array_merge( $dirs, [ __DIR__ ] );
	}
);
