<?php

namespace YP;

class PostsControllerTickets extends \WP_REST_Posts_Controller {

  private $postTypeData = [
    'post_type' => 'yp_ticket',
    'taxonomy' => [
      'ticket_status',
      'ticket_priority',
      'ticket_service',
    ],
    'primary_taxonomy' => 'priority',
    'posts_per_page' => -1,
  ];

  /**
   * get the items
   *
   * @author Ynah Pantig
   * @param $data
   * @return
   */

  public function get_items( $data )
  {

    // get the parameters/arguments of the query
    $params = $data->get_params();
    $data = [];

    $taxonomy = $this->postTypeData[ 'primary_taxonomy' ];
    $postsPerPage = $this->postTypeData[ 'posts_per_page' ];
    $paged = get_query_var( 'paged' ) == 0 ? 1 : get_query_var( 'paged' );

    $currentUserMeta = get_userdata( get_current_user_id() );
    $currentUserRoles = $currentUserMeta->roles;

    $args = [
      'per_page' => $postsPerPage,
      'posts_per_page' => $postsPerPage,
      'paged' => $paged,
      'page' => $paged,
      'author__in' => [ $currentUserMeta->ID ],
      'post_type' => $this->postTypeData[ 'post_type' ],
    ];

    if ( \YP\Users::allowedAdminUser( $currentUserRoles ) ) {
      unset( $args[ 'author__in' ] );
    }

    $taxQuery = [];
    $isFiltered = false;
    if ( isset( $params[ 'filters' ] ) && !empty( $params[ 'filters' ] ) )
    {
      foreach ( $params[ 'filters' ] as $slug => $terms ) {
        if ( $terms == '' ) {
          continue;
        }

        if ( $terms == 'all' || $terms == 'disabled' ) {
          continue;
        }

        $taxQuery[] = [
          'taxonomy' => 'ticket_' . $slug,
          'field' => 'slug',
          'terms' => $terms,
        ];
      }

      unset( $params[ 'filters' ] );
    }

    if ( !empty( $taxQuery ) ) {
      $args[ 'tax_query' ] = $taxQuery;
    }

    if ( !empty( $params ) ) {
      $args = array_merge( $args, $params );
    }

    $query = new \WP_Query( $args );
    $posts = $query->posts;

    $settings = \YP\Admin::pluginSettings();
    $ticketFormURL = filter_var( get_permalink( $settings[ 'form_submit' ] ),  FILTER_SANITIZE_URL );
    $formatLink = '<a href="' . $ticketFormURL . '">Click here to submit a ticket</a>';

    $noResults = __( 'You currently do not have any open tickets. %s' );
    $noResults = sprintf( $noResults, $formatLink );

    $data = [
      'no_results' => $noResults,
      // 'new_ticket_count' => get_user_meta( $currentUserMeta->ID, 'yp_new_tickets', 1 ),
      'is_admin_user' => \YP\Users::allowedAdminUser( $currentUserRoles ),
    ];

    if( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();

      $post = $query->post;
      $postAuthor = $post->post_author;
      $terms = [];

      foreach ( $this->postTypeData[ 'taxonomy' ] as $item ) {
        $terms[ $item ] = get_the_terms( $post, $item )[0];
        $terms[ $item ]->taxonomy = get_taxonomy( $item );
      }

      $post->terms = (object) $terms;
      $post->custom = (object) [
        'excerpt' => wpautop( wp_trim_excerpt( $post->post_content ) ),
        'ticket_status' => [
          'new' => (bool) get_post_meta( $post->ID, 'is_new_ticket', 1 ),
          'updated' => (bool) get_post_meta( $post->ID, 'is_commented_ticket', 1 )
        ],
        'new_ticket' => [
          'admin' => (bool) get_post_meta( $post->ID, 'new_ticket_admin', 1 ),
          'author' => (bool) get_post_meta( $post->ID, 'new_ticket_author', 1 ),
        ]
      ];

      $post->permalink = get_permalink( $post );

      $data[ 'results' ][] = $this->prepare_response_for_collection( $query->post );

    endwhile; wp_reset_postdata(); endif;

    return $data;

  }


}
