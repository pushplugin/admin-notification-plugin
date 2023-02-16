<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pushplugin.com/admin-notification
 * @since      1.0.0
 *
 * @package    Pushplugin_Admin_Notification
 * @subpackage Pushplugin_Admin_Notification/admin/partials
 */

?>
<script>
  const applicationServerKey = '<?= get_option("pushplugin_admin_notification_vapid_public_key") ?>';
  const serviceWorkerFile = '<?= "/pushplugin-admin-notification-sw.js?ver=".filemtime(ABSPATH.'pushplugin-admin-notification-sw.js') ?>';
  const saveTokenPath = '<?= admin_url('admin-ajax.php').'?action=pushplugin_admin_notification_save_token' ?>';
  const revokeTokenPath = '<?= admin_url('admin-ajax.php').'?action=pushplugin_admin_notification_revoke_token' ?>';
</script>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
  <h1>Pushplugin Admin Notification</h1>

  <form method="post" action="options.php" novalidate="novalidate">
    <?php settings_fields( 'pushplugin-admin-notification' ); ?>  

    <table class="form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row"><label for="blogname">Give Permission to receive Notification in this browser.</label></th>
          <td>
            <label for="users_can_register">
              <button type="button" id="pushplugin-admin-notification-subscription-button" class="button button-primary" style="display: flex;align-items: center;">
                <div class="dashicons dashicons-megaphone" style="margin-right: 10px;" aria-hidden="true"></div>
                Allow
              </button>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="blogname">Revoke All the permissions.</label></th>
          <td>
            <label for="users_can_register">
              <button type="button" id="pushplugin-admin-notification-revoke-tokens-button" class="button button-secondary" style="display: flex;align-items: center;">
                Revoke all the permission
              </button>
            </label>
            <p class="description" id="tagline-description">If you click this, all your devices will get unsubscribed and you have to subscribe again.</p>
          </td>
        </tr>
        <tr class="option-site-visibility">
          <th scope="row">Notify me when a post is</th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span>Notify me when a post is</span></legend>
                <label for="post_publish"><input name="post_publish" type="checkbox" id="post_publish" <?= (get_option('pushplugin_admin_notification')['post_publish']? 'checked' : '') ?>>
                  Published
                </label><br>
                <label for="post_future"><input name="post_future" type="checkbox" id="post_future" <?= (get_option('pushplugin_admin_notification')['post_future']? 'checked' : '') ?>>
                  Scheduled for publishing
                </label><br>
                <label for="post_draft"><input name="post_draft" type="checkbox" id="post_draft" <?= (get_option('pushplugin_admin_notification')['post_draft']? 'checked' : '') ?>>
                  Draft
                </label><br>
                <label for="post_pending"><input name="post_pending" type="checkbox" id="post_pending" <?= (get_option('pushplugin_admin_notification')['post_pending']? 'checked' : '') ?>>
                  Pending Review
                </label><br>
                <label for="post_trash"><input name="post_trash" type="checkbox" id="post_trash" <?= (get_option('pushplugin_admin_notification')['post_trash']? 'checked' : '') ?>>
                  Trash (moved to Trash)
                </label>
            </fieldset>
          </td>
        </tr>
        <tr class="option-site-visibility">
          <th scope="row">Notify me when a page is</th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span>Notify me when a post is</span></legend>
                <label for="page_publish"><input name="page_publish" type="checkbox" id="page_publish" <?= (get_option('pushplugin_admin_notification')['page_publish']? 'checked' : '') ?>>
                  Published
                </label><br>
                <label for="page_future"><input name="page_future" type="checkbox" id="page_future" <?= (get_option('pushplugin_admin_notification')['page_future']? 'checked' : '') ?>>
                  Scheduled for publishing
                </label><br>
                <label for="page_draft"><input name="page_draft" type="checkbox" id="page_draft" <?= (get_option('pushplugin_admin_notification')['page_draft']? 'checked' : '') ?>>
                  Draft
                </label><br>
                <label for="page_pending"><input name="page_pending" type="checkbox" id="page_pending" <?= (get_option('pushplugin_admin_notification')['page_pending']? 'checked' : '') ?>>
                  Pending Review
                </label><br>
                <label for="page_trash"><input name="page_trash" type="checkbox" id="page_trash" <?= (get_option('pushplugin_admin_notification')['page_trash']? 'checked' : '') ?>>
                  Trash (moved to Trash)
                </label>
            </fieldset>
          </td>
        </tr>
        <tr class="option-site-visibility">
          <th scope="row">Notify me when a comment is</th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span>Notify me when a comment is</span></legend>
                <label for="comment_unapproved"><input name="comment_unapproved" type="checkbox" id="comment_unapproved" <?= (get_option('pushplugin_admin_notification')['comment_unapproved']? 'checked' : '') ?>>
                  Hold
                </label><br>
                <label for="comment_approved"><input name="comment_approved" type="checkbox" id="comment_approved" <?= (get_option('pushplugin_admin_notification')['comment_approved']? 'checked' : '') ?>>
                  Approved
                </label><br>
                <label for="comment_spam"><input name="comment_spam" type="checkbox" id="comment_spam" <?= (get_option('pushplugin_admin_notification')['comment_spam']? 'checked' : '') ?>>
                  Spam
                </label><br>
                <label for="comment_trash"><input name="comment_trash" type="checkbox" id="comment_trash" <?= (get_option('pushplugin_admin_notification')['comment_trash']? 'checked' : '') ?>>
                  Trash
                </label>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
  </form>
</div>