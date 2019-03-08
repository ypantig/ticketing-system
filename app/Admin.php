<?php

namespace YP;

class Admin {

  private $options = 'yp_tickets_data';

  public function __construct() {

    add_action( 'admin_menu', [ &$this, 'admin_menu__createAdminMenuItem' ] );
    add_action( 'wp_ajax_store_admin_data', [ &$this, 'wp_ajax_store_admin_data__storeAdminData' ] );

    add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts__enqueueStyles' ] );

    add_action( 'admin_notices', [ &$this, 'admin_notices__addAdminNotice' ] );

    add_action( 'admin_init', [ &$this, 'admin_init__allowRolesToUpload' ] );
    add_action( 'admin_init', [ &$this, 'admin_init__initPluginSettings' ] );
  }

  public function admin_init__allowRolesToUpload() {
    $roles = [
      'administrator',
      'um_property-manager',
      'um_council-member',
    ];

    foreach ( $excludeRoles as $role ) {
      $role = get_role( $role );

      if ( ! $role->has_cap( 'upload_files' ) ) {
        $role->add_cap( 'upload_files' );
      }
    }
  }

  public function admin_notices__addAdminNotice() {

    global $ticketPostType;

    if ( is_plugin_inactive( 'comment-attachment/comment-attachment.php' ) ) {
      echo '<div class="notice"><p>Please install <a href="https://wordpress.org/plugins/comment-attachment/" target="_blank">Comment Attachment</a> plugin to allow users to upload attachments to their tickets.</p></div>';
    }

  }

  /**
   * register settings
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function admin_init__initPluginSettings() {

    register_setting( 'yp_ticket_system', 'yp_ticket_success_message' );
    register_setting( 'yp_ticket_system', 'yp_ticket_error_message' );
    register_setting( 'yp_ticket_system', 'yp_ticket_notification_from_name' );
    register_setting( 'yp_ticket_system', 'yp_ticket_notification_from_email' );

  }

  /**
   * create an admin menu for the ticketing system
   *
   * @author Ynah Pantig <me@ynahpantig.com>
   */
  public function admin_menu__createAdminMenuItem() {

    global $ticketPostType;

    add_submenu_page(
      'edit.php?post_type=' . $ticketPostType,
      __( 'Settings', 'yp-ticketing-system' ),
      __( 'Settings', 'yp-ticketing-system' ),
      'manage_options',
      'yp-ticketing-system',
      [ &$this, 'adminTicketingSystem' ],
      ''
    );

  }

  /**
   * outputs the admin dashboard containing options
   *
   * @author Ynah Pantig <me@ynahpantig.com>
   */
  public function adminTicketingSystem() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
      return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
      // add settings saved message with the class of "updated"
      add_settings_error( 'yp_ticket_system_messages', 'yp_ticket_system_message', __( 'Settings Saved', 'yp_ticket_system' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'yp_ticket_system_messages' );

    ?>

    <div class="wrap">

      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

      <form action="options.php" method="post">

        <?php

          // output security fields for the registered setting "yp_ticket_system"
          settings_fields( 'yp_ticket_system' );

          // output setting sections and their fields
          // (sections are registered for "yp_ticket_system", each field is registered to a specific section)
          do_settings_sections( 'yp_ticket_system' );

          $success = esc_attr( get_option( 'yp_ticket_success_message' ) );

          if ( $success == '' ) {
            $success = __( 'Your ticket has been submitted. We will get back to you as soon as we can.' );
          }

          $error = esc_attr( get_option( 'yp_ticket_error_message' ) );

          if ( $error == '' ) {
            $error = __( 'There\'s something wrong with submitting the form. Please try agian later.' );
          }

          $email = [
            'from' => [
              'name' => get_option( 'yp_ticket_notification_from_name' ) ? esc_attr( get_option( 'yp_ticket_notification_from_name' ) ) : get_option( 'blogname' ),
              'email' => get_option( 'yp_ticket_notification_from_email' ) ? esc_attr( get_option( 'yp_ticket_notification_from_email' ) ) : get_option( 'admin_email' ),
            ],
          ];

        ?>

        <div class="inner">
          <table class="form-table">
            <tr>
              <th scope="row"><h3 style="margin-bottom: 0; margin-top: 0;">Messages</h3></th>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="yp_ticket_success_message"><?php echo __( 'Success Message' ); ?></label>
              </th>
              <td>
                <input type="text" class="regular-text" name="yp_ticket_success_message" value="<?php echo $success; ?>" />
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="yp_ticket_error_message"><?php echo __( 'Error Message' ); ?></label>
              </th>
              <td>
                <input type="text" class="regular-text" name="yp_ticket_error_message" value="<?php echo $error; ?>" />
              </td>
            </tr>
          </table>

          <table class="form-table">
            <tr>
              <th scope="row"><h3 style="margin-bottom: 0; margin-top: 0;">Email Notification</h3></th>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="yp_ticket_notification_from_name"><?php echo __( 'From Name' ); ?></label>
              </th>
              <td>
                <input type="text" class="regular-text" name="yp_ticket_notification_from_name" value="<?php echo $email[ 'from' ][ 'name' ]; ?>" />
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="yp_ticket_notification_from_email"><?php echo __( 'From Email' ); ?></label>
              </th>
              <td>
                <input type="text" class="regular-text" name="yp_ticket_notification_from_email" value="<?php echo $email[ 'from' ][ 'email' ]; ?>" />
              </td>
            </tr>
          </table>
        </div><!-- .inner -->

        <?php

          // output save settings button
          submit_button( 'Save Settings' );

        ?>

      </form>

    </div>

    <?php

    $data = $this->getData();

  }

  /**
   * Returns the saved options data as an array
   *
   * @author Ynah Pantig <me@ynahpantig.com>
   */
  public function getData() {

    return get_option( $this->options, [] );

  }

  public function admin_enqueue_scripts__enqueueStyles() {

    // enqueue the custom dashicon
    wp_register_style( 'yp/ticket_system/dashicons', YP_TICKETS_URL . '/assets/fonts/dashicon/css/yp-ticketing-system.css' );

    wp_enqueue_style( 'yp/ticket_system/dashicons' );

    // enqueue the styles for the fields
    wp_register_style( 'yp/ticket_system/styles/admin', YP_TICKETS_URL . '/dist/styles/admin.css' );

    wp_enqueue_style( 'yp/ticket_system/styles/admin' );

  }

}
