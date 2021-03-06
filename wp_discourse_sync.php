<?php

/**
 * @link              https://github.com/hanzketup/wp_discourse_sync
 * @since             1.0.0
 * @package           WP Discourse Sync
 *
 * @wordpress-plugin
 * Plugin Name:       WP Discourse Sync
 * Plugin URI:        https://github.com/hanzketup/wp_discourse_sync
 * Description:       Wordpress plugin that 'cross-posts' discourse topics to wordpress blog posts.
 * Version:           1.1.1
 * Author:            Hanzketup
 * Author URI:        https://hanneshermansson.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       WP-Discourse-Sync
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'wp_discourse_sync_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-discourse-sync-activator.php
 */
function activate_wp_discourse_sync() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-discourse-sync-activator.php';
	wp_discourse_sync_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-discourse-sync-deactivator.php
 */
function deactivate_wp_discourse_sync() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-discourse-sync-deactivator.php';
	wp_discourse_sync_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_discourse_sync' );
register_deactivation_hook( __FILE__, 'deactivate_wp_discourse_sync' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-discourse-sync.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_discourse_sync() {

	$plugin = new wp_discourse_sync();
	$plugin->run();

}

run_wp_discourse_sync();
