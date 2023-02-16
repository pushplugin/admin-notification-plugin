<?php

/**
 * Fired during plugin activation
 *
 * @link       https://pushplugin.com/admin-notification
 * @since      1.0.0
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/includes
 * @author     PushPlugin Admin <admin@pushplugin.com>
 */
class Pushplugin_Admin_Notification_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Copy Service Worker File
		$source = PUSHPLUGIN_ADMIN_NOTIFICATION_UNIQUE_PATH . 'admin/js/pushplugin-admin-notification-sw.js';
		$destination = ABSPATH . 'pushplugin-admin-notification-sw.js';
		copy($source, $destination);
	}

}
