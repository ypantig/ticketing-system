<?php

namespace YP;

class TermsControllerTicketPriority extends \WP_REST_Terms_Controller {

  private $tax = 'ticket_priority';

  public function __construct() {
    parent::__construct( $this->tax );
  }

  public function get_items( $request ) {

    $items = parent::get_items( $request );
    $response = array();

    if ( is_wp_error( $items ) || empty( $items->get_data() ) ) {
      $terms = \App\Posts::getTermsQuery( $this->tax, [
        'hide_empty' => false,
      ]);

      foreach ( $terms as $item ) {
        $response[] = $this->prepare_response_for_collection( $item );
      }

    } else {

      $response = $items->get_data();

    }

    $taxObject = get_taxonomy( $this->tax );

    $return = [
      'terms' => $response,
      'label' => $taxObject->labels->singular_name,
      'placeholder' => __( 'Filter by', 'yp-ticketing-system' ) . ' ' . $taxObject->labels->singular_name,
      'slug' => $this->tax,
    ];

    return $return;

  }

}
