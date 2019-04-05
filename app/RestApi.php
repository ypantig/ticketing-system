<?php

namespace YP;

class RestApi {

  public function __construct() {

    add_action( 'rest_api_init', [ &$this, 'rest_api_init__registerEndPoints' ] );

  }

  public function rest_api_init__registerEndPoints() {

    // Here we are registering our route for a collection of products and creation of products.
    register_rest_route( 'tickets/v1', '/all', [
      [
        // By using this constant we ensure that when the WP_REST_Server changes, our readable endpoints will work as intended.
        'methods'  => 'GET',
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => [ '\\YP\\Tickets', 'getAllTickets' ],
      ],
    ]);

  }

}
