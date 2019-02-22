<?php

namespace YP;

class Admin {

  private $options = 'yp_tickets_data';

  public function __construct() {

    add_action( 'admin_menu', [ &$this, 'admin_menu__createAdminMenuItem' ] );
    add_action( 'wp_ajax_store_admin_data', [ &$this, 'wp_ajax_store_admin_data__storeAdminData' ] );

    add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts__enqueueStyles' ] );

    add_action( 'admin_notices', [ &$this, 'admin_notices__addAdminNotice' ] );
    // add_action( 'admin_menu', [ &$this, 'admin_menu__registerSettings' ] );

    add_action('admin_init', [ &$this, 'admin_init__allowRolesToUpload' ] );
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

    global $pagenow, $ticketPostType;

    if ( $pagenow == 'edit.php' && get_post_type() == $ticketPostType ) {
      if ( is_plugin_inactive( 'comment-attachment/comment-attachment.php' ) ) {
        echo '<div class="notice"><p>Please install <a href="https://wordpress.org/plugins/comment-attachment/" target="_blank">Comment Attachment</a> plugin to allow users to upload attachments to their tickets.</p></div>';
      }
    }

  }

  /**
   * register settings
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function admin_menu__registerSettings() {

    // register_setting( 'yp_tickets_settings', 'yp_tickets_settings_basic', $args = array )

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
      __( 'Options', 'yp-ticketing-system' ),
      __( 'Options', 'yp-ticketing-system' ),
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

    $app = new \YPTicketingSystem();
    $data = $app->getData();

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
