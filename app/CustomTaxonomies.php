<?php

namespace YP;

class CustomTaxonomies {
  protected $taxonomies = [
    'ticket_status' => [
      'post_type'         => [ 'yp_ticket' ],
      'singular'          => 'Status',
      'plural'            => 'Statuses',
      'labels'            => [],
      'args' => [
        'show_in_rest' => true,
        'rest_base' => 'ticket_status',
        'rest_controller_class' => __NAMESPACE__ . '\TermsControllerTicketStatus',
      ],
    ],
    'ticket_priority' => [
      'post_type'         => [ 'yp_ticket' ],
      'singular'          => 'Priority',
      'plural'            => 'Priorities',
      'labels'            => [],
      'args' => [
        'show_in_rest' => true,
        'rest_base' => 'ticket_priority',
        'rest_controller_class' => __NAMESPACE__ . '\TermsControllerTicketPriority',
      ],
    ],
    'ticket_service' => [
      'post_type'         => [ 'yp_ticket' ],
      'singular'          => 'Service',
      'labels'            => [],
      'args' => [
        'show_in_rest' => true,
        'rest_base' => 'ticket_service',
        'rest_controller_class' => __NAMESPACE__ . '\TermsControllerTicketService',
      ],
    ],
  ];


  /**
   * Default arguments to use for register_post_type
   *
   * @var array
   */
  protected $defaultArgs = [
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => true,
    'hierarchical'      => true,
    'show_tagcloud'     => false,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
    'capabilities'      => [],
  ];

  function __construct(  )
  {

    // register accommodation post type
    add_action( 'init', [ &$this, 'init__registerTaxonomies' ], 1 );

  }/* __construct() */


  /**
   * register accommodation post type
   *
   * @author Ynah Pantig
   * @package
   * @since 1.0
   * @param
   * @return
   */

  public function init__registerTaxonomies()
  {

    foreach ( $this->taxonomies as $slug => $args ) {
      $this->dynamicRegisterTaxonomy( $slug, $args );
    }

  }/* init__registerTaxonomies() */

  /**
   * get all the terms from the taxonomy passed as parameter
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public static function getTermsFromTaxonomy( $taxonomy ) {

    if ( empty( $taxonomy ) ) {
      return new \WP_Error( 'broke', __( 'Taxonomy is required', 'yp-ticketing-system' ) );
    }

    $args = [
      'hide_empty' => false,
      'taxonomy' => $taxonomy,
    ];

    $terms = new \WP_Term_Query( $args );

    return $terms->terms;

  }

  /**
   * dynamically create the post types based on the array
   *
   * @author Ynah Pantig
   * @package CustomTaxonomies.php
   * @since 1.0
   * @param $slug, $args
   * @return $data
   */

  public function dynamicRegisterTaxonomy( $slug, $customArgs )
  {

    if ( isset( $customArgs[ 'args' ] ) && !empty( $customArgs[ 'args' ] ) ) {
      $args = array_merge( $this->defaultArgs, $customArgs[ 'args' ] );
    } else {
      $args = $this->defaultArgs;
    }

    $postName = str_replace( '-', ' ', $slug );
    $singular = ucwords( $postName );

    if ( !empty( $customArgs[ 'singular' ] ) ) {
      $singular = $customArgs[ 'singular' ];
    }

    if ( !empty( $customArgs[ 'plural' ] ) ) {
      $plural = $customArgs[ 'plural' ];
    } else {
      $plural = ucwords( $singular ) . 's';
    }

    $labels = array(
      'name'                  => __( $plural, 'Taxonomy plural name', 'yp-ticketing-system' ),
      'singular_name'         => __( $singular, 'Taxonomy singular name', 'yp-ticketing-system' ),
      'search_items'          => __( 'Search ' . $plural, 'yp-ticketing-system' ),
      'popular_items'         => __( 'Popular ' . $plural, 'yp-ticketing-system' ),
      'all_items'             => __( 'All ' . $plural, 'yp-ticketing-system' ),
      'parent_item'           => __( 'Parent ' . $singular, 'yp-ticketing-system' ),
      'parent_item_colon'     => __( 'Parent ' . $singular, 'yp-ticketing-system' ),
      'edit_item'             => __( 'Edit ' . $singular, 'yp-ticketing-system' ),
      'update_item'           => __( 'Update ' . $singular, 'yp-ticketing-system' ),
      'add_new_item'          => __( 'Add New ' . $singular, 'yp-ticketing-system' ),
      'new_item_name'         => __( 'New ' . $singular, 'yp-ticketing-system' ),
      'add_or_remove_items'   => __( 'Add or remove ' . $plural, 'yp-ticketing-system' ),
      'choose_from_most_used' => __( 'Choose from most used yp-ticketing-system', 'yp-ticketing-system' ),
      'menu_name'             => __( $plural, 'yp-ticketing-system' ),
    );

    if ( !empty( $customArgs[ 'labels' ] ) ) {
      $labels = array_merge( $labels, $customArgs[ 'labels' ] );
    }

    $args[ 'labels' ] = $labels;

    register_taxonomy( $slug, $customArgs[ 'post_type' ], $args );

  }/* dynamicRegisterTaxonomy() */

}
