<?php
require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://pushplugin.com/admin-notification
 * @since             1.0.0
 * @package           Pushplugin_Admin_Notification
 *
 * @wordpress-plugin
 * Plugin Name:       PushPlugin Admin Notification
 * Plugin URI:        https://pushplugin.com/admin-notification
 * Description:       This plugin is for sending push notification to admin when ever an event occurs and admin has subscribed to that event.
 * Version:           1.0.0
 * Author:            PushPlugin Admin
 * Author URI:        https://pushplugin.com/admin-notification
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pushplugin-admin-notification
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
define( 'PUSHPLUGIN_ADMIN_NOTIFICATION_VERSION', '1.0.0' );

/**
 * Current Plugin Directory and Current Plugin Path
 */
$url = plugin_dir_url(__FILE__);
define('PUSHPLUGIN_ADMIN_NOTIFICATION_UNIQUE_URL', $url);

$path = plugin_dir_path(__FILE__);
define('PUSHPLUGIN_ADMIN_NOTIFICATION_UNIQUE_PATH', $path);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pushplugin-admin-notification-activator.php
 */
function activate_pushplugin_admin_notification() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pushplugin-admin-notification-activator.php';
	Pushplugin_Admin_Notification_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pushplugin-admin-notification-deactivator.php
 */
function deactivate_pushplugin_admin_notification() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pushplugin-admin-notification-deactivator.php';
	Pushplugin_Admin_Notification_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pushplugin_admin_notification' );
register_deactivation_hook( __FILE__, 'deactivate_pushplugin_admin_notification' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pushplugin-admin-notification.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pushplugin_admin_notification() {

	$plugin = new Pushplugin_Admin_Notification();
	$plugin->run();

}
run_pushplugin_admin_notification();
