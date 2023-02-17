<?php

/**
 * The admin-notification-specific functionality of the plugin.
 *
 * @link       https://pushplugin.com/admin-notification
 * @since      1.0.0
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/admin
 */

/**
 * The admin-notification-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-notification-specific stylesheet and JavaScript.
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/admin
 * @author     PushPlugin Admin <admin@pushplugin.com>
 */

use \Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class Pushplugin_Admin_Notification_Admin_SendNotification {
    /**
     * Create Vapid Key
     * 
     * @since 1.0.0
     * @access public
     */
    public static function createVapidKeys(){
        if(get_option('pushplugin_admin_notification_vapid_public_key') == '' || get_option('pushplugin_admin_notification_vapid_private_key') == ''){
            $vapid = VAPID::createVapidKeys();
			update_option('pushplugin_admin_notification_vapid_public_key', $vapid['publicKey']);
			update_option('pushplugin_admin_notification_vapid_private_key', $vapid['privateKey']);
		}
    }

    /**
     * Saves Token
     * 
     * @since 1.0.0
     * @access public
     */
    public static function saveToken($token){
        $tokens = get_option('pushplugin_admin_notification_tokens', '[]');
        $tokens = json_decode($tokens, true);

        // check if token already exists
        if(in_array($token, $tokens)){
            return;
        }
        
        $tokens[] = $token;
        $tokens = json_encode($tokens);
        update_option('pushplugin_admin_notification_tokens', $tokens);
    }

    /**
     * Deletes Token
     * 
     * @since 1.0.0
     * @access public
     */
    public static function deleteToken($token){
        $tokens = get_option('pushplugin_admin_notification_tokens', '[]');
        $tokens = json_decode($tokens, true);

        $newTokens = [];
        foreach($tokens as $t){
            if($t['endpoint'] != $token['endpoint']){
                $newTokens[] = $t;
            }
        }

        $tokens = json_encode($newTokens);
        update_option('pushplugin_admin_notification_tokens', $tokens);
    }

    /**
     * Deletes All Tokens
     * 
     * @since 1.0.0
     * @access public
     */
    public static function deleteAllTokens(){
        update_option('pushplugin_admin_notification_tokens', '[]');

    }

    /**
     * Sends Notification
     * 
     * @since 1.0.0
     * @access public
     */
    public static function sendNotification($title, $body){
        $tokens = get_option('pushplugin_admin_notification_tokens', '[]');
        $tokens = json_decode($tokens, true);
        $auth = array(
            'VAPID' => array(
                'subject' => get_bloginfo('url'),
                'publicKey' => get_option('pushplugin_admin_notification_vapid_public_key'),
                'privateKey' => get_option('pushplugin_admin_notification_vapid_private_key'),
            ),
        );
        $body = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => 'https://cdn.pushplugin.com/general/icon-red.svg',
            'data' => [
                'url' =>  admin_url(),
            ]

        ]);
        $webPush = new WebPush($auth);
        $reports = [];
        foreach ($tokens as $token) {
            $report = $webPush->sendNotification(
                Subscription::create($token),
                $body
            );
            $reports[] = $report;
        }

        // handle eventual errors here, and remove the subscription from your server if it is expired
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            // if ($report->isSuccess()) {
            //     echo "[v] Message sent successfully for subscription {$endpoint}.";
            // } else {
            //     echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
            // }
        }
    }
}