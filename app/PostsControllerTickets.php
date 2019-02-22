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

    $args = [
      'per_page' => $postsPerPage,
      'posts_per_page' => $postsPerPage,
      'paged' => $paged,
      'page' => $paged,
      'post_author' => get_current_user_id(),
      'post_type' => $this->postTypeData[ 'post_type' ],
    ];

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
    $key = 0;

    $data = [
      'no_results' => __( 'No results to show' ),
      'query' => $query
    ];

    if( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();

      $post = $query->post;
      $terms = [];

      foreach ( $this->postTypeData[ 'taxonomy' ] as $item ) {
        $terms[ $item ] = get_the_terms( $post, $item )[0];
        $terms[ $item ]->taxonomy = get_taxonomy( $item );
      }

      $post->terms = (object) $terms;
      $post->custom = (object) [
        'excerpt' => wpautop( wp_trim_excerpt( $post->post_content ) ),
      ];
      $post->key = $key;

      $data[ 'results' ][] = $this->prepare_response_for_collection( $query->post );

      $key++;

    endwhile; wp_reset_postdata(); endif;

    return $data;

  }


}
