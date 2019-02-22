<?php

namespace YP;

class CustomFields {

  public function __construct() {

    add_action( 'add_meta_boxes', [ &$this, 'add_meta_boxes__addMetaBoxForCustomFields' ] );

  }

  /**
   * add meta box for custom fields
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function add_meta_boxes__addMetaBoxForCustomFields() {

    global $ticketPostType;
    add_meta_box( 'tickets-information', 'Information', [ &$this, 'ticketInformation' ], $ticketPostType, 'normal', 'high' );

  }

  /**
   * callback for adding meta boxes to hold custom fields
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function ticketInformation() {

    include YP_TICKETS_PATH . 'assets/views/post-fields.php';

  }

}
