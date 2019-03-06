<?php

namespace YP;

class Theme {

  public function __construct() {

    add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts__addStyles' ] );
    add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts__addScripts' ], 100 );

  }

  public function body_class__addBodyClass( $classes ) {

    $classes[] = 'yp-ticketing-system';

    return $classes;

  }

  public function wp_enqueue_scripts__addStyles() {

    wp_enqueue_style('yp/ticket_system/styles/main', YP_TICKETS_URL . 'dist/styles/main.css', false, null);

  }

  public function wp_enqueue_scripts__addScripts() {

    wp_enqueue_script( 'yp/ticket_system/scripts/main', YP_TICKETS_URL . 'dist/scripts/main.js', ['jquery'], null, true );

    $ajaxUrl = admin_url( 'admin-ajax.php' );

    $localize = array(
      'ajax_url' => $ajaxUrl,
      'upload_url' => admin_url( 'async-upload.php' ),
      'nonce' => wp_create_nonce( 'media-form' ),
      'query_vars' => json_encode( $wp_query->query ),
      // 'nonce' => wp_create_nonce( 'wp_rest') //secret value created every time you log in and can be used for authentication to alter content
    );

    wp_enqueue_script( 'wp-api' );
    wp_localize_script( 'yp/ticket_system/scripts/main', 'yp_ticket_global', $localize );

  }

  public function single_template__loadCustomSingle( $template ) {

    global $post, $ticketPostType;

    if ( $post->post_type == $ticketPostType ) {
      $template = dirname( dirname( __FILE__ ) ) . '/assets/views/single.php';
    }

    return $template;

  }

}
