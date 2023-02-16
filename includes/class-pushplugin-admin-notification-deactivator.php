<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://pushplugin.com/admin-notification
 * @since      1.0.0
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/includes
 * @author     PushPlugin Admin <admin@pushplugin.com>
 */
class Pushplugin_Admin_Notification_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Removing Service Worker File
		$destination = ABSPATH . 'pushplugin-admin-notification-sw.js';
		unlink($destination);
	}

}
