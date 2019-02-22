<?php

namespace YP;

class CustomPostTypes {

  /**
   * Place post types here.
   * Singular and plural will be used to auto generate labels.
   * Labels and args will overwrite defaults.
   *
   * @var array
   */
  protected $postTypes = [
    'yp_ticket' => [
      'singular' => 'Ticket',
      'labels'   => [],
      'args'     => [
        'menu_icon'   => 'dashicons-customer-service',
        'show_in_rest' => true,
        'rest_controller_class' => __NAMESPACE__ . '\PostsControllerTickets',
        'rewrite' => [
          'slug' => 'ticket',
          'with_front' => true,
        ],
        'supports'    => [
          'title',
          'comments',
          'editor',
        ],
      ],
    ],
  ];

  /**
   * Default arguments to use for register_post_type
   *
   * @var array
   */
  protected $defaultArgs = [
    'public'              => true,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'hierarchical'        => false,
    'has_archive'         => false,
    'publicly_queryable'  => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'show_in_rest'        => false,
  ];

  public function __construct(  )
  {

    // register accommodation post type
    add_action( 'init', [ &$this, 'init__registerPostTypes' ], 1 );

    add_filter( 'enter_title_here', [ &$this, 'enter_title_here__changePlaceholder' ] );

  }/* __construct() */

  /**
   * change the "enter title here" placeholder
   *
   * @author Ynah Pantig
   * @package
   * @since 1.0
   * @param
   * @return
   */

  public function enter_title_here__changePlaceholder( $title )
  {

    $screen = get_current_screen();

    switch ( $screen->post_type ) {
      case 'yp_ticket':
        $title = 'Enter Ticket Name';
      break;

      default:
        $title = 'Enter title here';
      break;
    }

    return $title;


  }/* enter_title_here__changePlaceholder() */

  /**
   * register accommodation post type
   *
   * @author Ynah Pantig
   * @package
   * @since 1.0
   * @param
   * @return
   */

  public function init__registerPostTypes()
  {

    foreach ( $this->postTypes as $slug => $args ) {
      $this->dynamicRegisterPostType( $slug, $args );
    }

  }/* init__registerPostTypes() */


  /**
   * dynamically create the post types based on the array
   *
   * @author Ynah Pantig
   * @package CustomPostTypes.php
   * @since 1.0
   * @param $slug, $customArgs
   * @return $data
   */

  public function dynamicRegisterPostType( $slug, $customArgs )
  {

    if ( isset( $customArgs[ 'args' ] ) && !empty( $customArgs[ 'args' ] ) ) {
      $args = array_merge( $this->defaultArgs, $customArgs['args'] );
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

    $labels = [
      'name'                => __( $plural, 'yp-ticketing-system' ),
      'singular_name'       => __( $singular, 'yp-ticketing-system' ),
      'add_new'             => _x( 'Add New ' . $singular, 'yp-ticketing-system' ),
      'add_new_item'        => __( 'Add New ' . $singular, 'yp-ticketing-system' ),
      'all_items'           => __( 'All ' . $plural, 'yp-ticketing-system' ),
      'edit_item'           => __( 'Edit ' . $singular, 'yp-ticketing-system' ),
      'new_item'            => __( 'New ' . $singular, 'yp-ticketing-system' ),
      'view_item'           => __( 'View ' . $singular, 'yp-ticketing-system' ),
      'search_items'        => __( 'Search ' . $plural, 'yp-ticketing-system' ),
      'not_found'           => __( 'No ' . $plural . ' found', 'yp-ticketing-system' ),
      'not_found_in_trash'  => __( 'No ' . $plural . ' found in Trash', 'yp-ticketing-system' ),
      'parent_item_colon'   => __( 'Parent :' . $singular, 'yp-ticketing-system' ),
      'menu_name'           => __( $plural, 'yp-ticketing-system' ),
    ];

    if ( !empty( $customArgs[ 'labels' ] ) ) {
      $labels = array_merge( $labels, $customArgs[ 'labels' ] );
    }

    $args[ 'labels' ] = $labels;

    register_post_type( $slug, $args );

  }/* dynamicRegisterPostType() */
}
