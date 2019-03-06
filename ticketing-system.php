<?php

/**
 * Plugin Name: YP Support Ticketing System
 * Description: Support Ticketing System for Atira Property Management Services Strata Sites
 * Version: 1.0
 * Author: Ynah Pantig
 * Author URI: https://ynahpantig.com
 * Text Domain: yp-ticketing-system
 *
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !defined( 'YP_TICKETS_URL' ) ) {
  define( 'YP_TICKETS_URL', plugin_dir_url( __FILE__ ) );
}

if( !defined( 'YP_TICKETS_PATH' ) ) {
  define( 'YP_TICKETS_PATH', plugin_dir_path( __FILE__ ) );
}

class YPTicketingSystem {

  /** @var string The plugin version number */
  var $version = '1.0';

  public function __construct() {

    if ( file_exists( $composer = __DIR__ . '/vendor/autoload.php' ) ) {
      require_once $composer;
    }

    global $ticketPostType;

    $ticketPostType = 'yp_ticket';

  }

  public function initialize() {

    new \YP\Admin();
    new \YP\Ajax();
    new \YP\CustomPostTypes();
    new \YP\CustomTaxonomies();
    new \YP\CustomFields();
    new \YP\Shortcodes();
    new \YP\Comments();
    new \YP\Theme();

  }

}

function loadTicketSystem() {

  // globals
  global $ypTickets;

  if ( !isset( $ypTickets ) ) {
    $ypTickets = new YPTicketingSystem();
    $ypTickets->initialize();
  }

  return $ypTickets;

}

loadTicketSystem();
