<?php

namespace YP;

class RestApi {

  protected $routes = [
    [
      'namespace' => 'tickets/v1',
      'route' => '/all',
      'args' => [
        'methods'  => 'GET',
        'callback' => [ '\\YP\\Tickets', 'getAllTickets' ],
      ],
    ],
    [
      'namespace' => 'tickets/v1',
      'route' => '/site/(?P<id>\d+)',
      'args' => [
        'methods'  => 'GET',
        'callback' => [ '\\YP\\Tickets', 'getSiteTicket' ],
      ],
    ]
  ];

  public function __construct() {

    add_action( 'rest_api_init', [ &$this, 'rest_api_init__registerEndPoints' ] );

  }

  public function rest_api_init__registerEndPoints() {

    foreach ( $this->routes as $route ) {
      register_rest_route( $route[ 'namespace' ], $route[ 'route' ], $route[ 'args' ] );
    }

  }

}
