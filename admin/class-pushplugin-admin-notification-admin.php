<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pushplugin.com/admin-notification
 * @since      1.0.0
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/admin
 * @author     PushPlugin Admin <admin@pushplugin.com>
 */
class Pushplugin_Admin_Notification_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the menu page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function scheduler_admin_actions() {
		
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		add_options_page( 'PushPlugin Admin', 'PushPlugin Admin', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	}

	/**
	 * Show Setup page for the plugin
	 * 
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		// Create Vapid Keys if not exist
		Pushplugin_Admin_Notification_Admin_SendNotification::createVapidKeys();

		include_once( 'partials/pushplugin-admin-notification-admin-display.php' );
	}

	/**
	 * Saves the token in the database
	 * 
	 * @since    1.0.0
	 */
	public function save_token() {
		$subscription = json_decode(file_get_contents('php://input'), true);

		$authToken = sanitize_text_field($subscription['authToken']);
		$contentEncoding = sanitize_text_field($subscription['contentEncoding']);
		$endpoint = sanitize_text_field($subscription['endpoint']);
		$publicKey = sanitize_text_field($subscription['publicKey']);

		$token = array(
			'authToken' => $authToken,
			'contentEncoding' => $contentEncoding,
			'endpoint' => $endpoint,
			'publicKey' => $publicKey
		);

		if($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT'){	
			Pushplugin_Admin_Notification_Admin_SendNotification::saveToken($token);
		}

		if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
			Pushplugin_Admin_Notification_Admin_SendNotification::deleteToken($token);
		}
	}

	/**
	 * Revokes all the tokens in the database
	 * 
	 * @since    1.0.0
	 */
	public function revoke_token() {
		Pushplugin_Admin_Notification_Admin_SendNotification::deleteAllTokens($token);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pushplugin_Admin_Notification_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushplugin_Admin_Notification_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pushplugin-admin-notification-admin.css', array(), filemtime(
			plugin_dir_path( __FILE__ ) . 'css/pushplugin-admin-notification-admin.css'
		), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pushplugin_Admin_Notification_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushplugin_Admin_Notification_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pushplugin-admin-notification-admin.js', array( 'jquery' ), filemtime(
			plugin_dir_path( __FILE__ ) . 'js/pushplugin-admin-notification-admin.js'
		), false );

	}

	/**
	 * Register the plugin settings
	 * 
	 * @since    1.0.0
	 */
	public function register_settings() {
		/**
		 * This function registers the settings for the plugin
		 */
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'update_options'));
	}

	/**
	 * Update the options
	 * 
	 * @since    1.0.0
	 */
	public function update_options() {
		/**
		 * This function updates the settings options for the plugin
		 * so that they can be used  to send notifications.
		 */
		$post_publish = sanitize_text_field(isset($_POST['post_publish'])? 1 : 0);
		$post_future = sanitize_text_field(isset($_POST['post_future'])? 1 : 0);
		$post_draft = sanitize_text_field(isset($_POST['post_draft'])? 1 : 0);
		$post_pending = sanitize_text_field(isset($_POST['post_pending'])? 1 : 0);
		$post_trash = sanitize_text_field(isset($_POST['post_trash'])? 1 : 0);
		$page_publish = sanitize_text_field(isset($_POST['page_publish'])? 1 : 0);
		$page_future = sanitize_text_field(isset($_POST['page_future'])? 1 : 0);
		$page_draft = sanitize_text_field(isset($_POST['page_draft'])? 1 : 0);
		$page_pending = sanitize_text_field(isset($_POST['page_pending'])? 1 : 0);
		$page_trash = sanitize_text_field(isset($_POST['page_trash'])? 1 : 0);
		$comment_unapproved = sanitize_text_field(isset($_POST['comment_unapproved'])? 1 : 0);
		$comment_approved = sanitize_text_field(isset($_POST['comment_approved'])? 1 : 0);
		$comment_spam = sanitize_text_field(isset($_POST['comment_spam'])? 1 : 0);
		$comment_trash = sanitize_text_field(isset($_POST['comment_trash'])? 1 : 0);

		$option = array(
			'post_publish' => $post_publish,
			'post_future' => $post_future,
			'post_draft' => $post_draft,
			'post_pending' => $post_pending,
			'post_trash' => $post_trash,
			'page_publish' => $page_publish,
			'page_future' => $page_future,
			'page_draft' => $page_draft,
			'page_pending' => $page_pending,
			'page_trash' => $page_trash,
			'comment_unapproved' => $comment_unapproved,
			'comment_approved' => $comment_approved,
			'comment_spam' => $comment_spam,
			'comment_trash' => $comment_trash,
		);

		update_option('pushplugin_admin_notification', $option);
	}

	/**
	 * Call when post or page status is changed
	 * 
	 * @since    1.0.0
	 */
	public function post_page_status_changed($new_status, $old_status, $post) {
		if ( ! empty( $_REQUEST['meta-box-loader'] ) ) { // phpcs:ignore
			return;
		}

		$post_type = $post->post_type;
		$title = $post->post_title;
		$title = implode(' ', array_slice(explode(' ', $title), 0, 5));
		$author_id = $post->post_author;

		$author = get_the_author_meta('display_name', $author_id);

		$option = get_option('pushplugin_admin_notification');

		// Checking for post updates
		if($post_type == 'post') {
			// Checking for post publish
			if($new_status == 'publish' && $option['post_publish']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Post Moved to Publish!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for post scheduled
			if($new_status == 'future' && $option['post_future']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Post Scheduled!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for post draft
			if($new_status == 'draft' && $option['post_draft']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Post Moved to Draft!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for post pending
			if($new_status == 'pending' && $option['post_pending']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Post Pending For Approval!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for post trash
			if($new_status == 'trash' && $option['post_trash']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Post Trashed!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
		}

		// Checking for page updates
		if($post_type == 'page') {
			// Checking for page publish
			if($new_status == 'publish' && $option['page_publish']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Page Published!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for page scheduled
			if($new_status == 'future' && $option['page_future']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Page Scheduled!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for page draft
			if($new_status == 'draft' && $option['page_draft']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Page Drafted!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for page pending
			if($new_status == 'pending' && $option['page_pending']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Page Pending For Approval!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
			// Checking for page trash
			if($new_status == 'trash' && $option['page_trash']) {
				Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Page Trashed!', "Title: " . $title . "\nAuthor: ". $author . "");
			}
		}
	}

	/**
	 * Call when comment status is changed
	 * 
	 * @since    1.0.0
	 */
	public function comment_status_changed($new_status, $old_status, $comment) {

		$option = get_option('pushplugin_admin_notification');

		// Checking for comment unapproved
		if($new_status == 'unapproved' && $option['comment_unapproved']) {
			Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Comment Unapproved!', 'Comment: ' . $comment->comment_content . '');
		}
		// Checking for comment approved
		if($new_status == 'approved' && $option['comment_approved']) {
			Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Comment Approved!', 'Comment: ' . $comment->comment_content . '');
		}
		// Checking for comment spam
		if($new_status == 'spam' && $option['comment_spam']) {
			Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Comment Marked As Spam!', 'Comment: ' . $comment->comment_content . '');
		}
		// Checking for comment trash
		if($new_status == 'trash' && $option['comment_trash']) {
			Pushplugin_Admin_Notification_Admin_SendNotification::sendNotification('Comment Trashed!', 'Comment: ' . $comment->comment_content . '');
		}
	}

	/**
	 * Sets a notice in the admin area to inform the user for any notifications
	 * 
	 * @since    1.0.0
	 */
	public function admin_notice() {
		$response = wp_remote_get('https://cdn.pushplugin.com/admin-notification/config.json');
		$body = json_decode($response['body'], true);

		// check if page is pushplugin-admin-notification
		if (isset($_GET['page']) && $_GET['page'] == 'pushplugin-admin-notification') {
			if(isset($body['settings_banner']) && $body['settings_banner'] == true){
				echo '<div class="notice" style="border: 0px; padding: 0px;">
					<p><a href="' . $body['link'] . '" target="_blank">
						<img src="' . $body['image'] . '" style="width: 100%;"/>
					</a></p>
				</div>';
			}
			return;
		}
	
		if(isset($body['banner']) && $body['banner'] == true){
			echo '<div class="notice" style="border: 0px; padding: 0px;">
				<p><a href="' . $body['link'] . '" target="_blank">
					<img src="' . $body['image'] . '" style="width: 100%;"/>
				</a></p>
			</div>';
		}

		if(get_option('pushplugin_admin_notification_tokens', '[]') == '[]'){
			echo '<div class="notice notice-warning is-dismissible">
				<p>Pushplugin Admin Notification is active. Please go to <a href="' . admin_url('options-general.php?page=pushplugin-admin-notification') . '">Settings > Pushplugin Admin Notification</a> to configure the plugin.</p>
			</div>';
		}
	}
}
