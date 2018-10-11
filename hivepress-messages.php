<?php
/**
 * Plugin Name: HivePress Messages
 * Plugin URI: https://hivepress.co/
 * Description: Private messages for HivePress plugin.
 * Version: 1.0.0
 * Author: HivePress
 * Author URI: https://hivepress.co/
 * Text Domain: hivepress-messages
 * Domain Path: /languages/
 *
 * @package HivePress/Messages
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register plugin path.
add_filter(
	'hivepress/core/plugin_paths',
	function( $paths ) {
		return array_merge( $paths, [ dirname( __FILE__ ) ] );
	}
);
