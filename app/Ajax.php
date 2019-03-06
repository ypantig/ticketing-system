<?php

namespace YP;

class Ajax {

  public function __construct() {

    add_action( 'wp_ajax_submit_ticket_form', [ &$this, 'wp_ajax_submitTicketForm' ] );
    add_action( 'wp_ajax_nopriv_submit_ticket_form', [ &$this, 'wp_ajax_submitTicketForm' ] );

  }

  public function wp_ajax_submitTicketForm() {

    // check_ajax_referer( 'yp-ticket-system', 'security' );

    $request = $_POST[ 'data' ];

    // Check that the nonce was set and valid
    if( !wp_verify_nonce( $request[ '_wpnonce' ], 'yp-ticket-system' ) ) {
      echo $data = json_encode([
        'error' => 'Did not save because your form seems to be invalid. Sorry',
        'request' => $_POST,
      ]);

      die();
    }

    // Stop running function if form wasn't submitted
    if ( !isset( $request[ 'post_title' ] ) ) {
      echo $data = json_encode([
        'error' => 'No post title',
        'request' => $_POST,
      ]);

      die();
    }

    /**
     * HANDLE CREATING THE POST/TICKET
     */
    $post = [
      'post_title' => $request[ 'post_title' ],
      'post_content' => $request[ 'post_content' ],
      'post_author' => $request[ 'post_author' ],
      'post_type' => 'yp_ticket',
      'post_status' => 'publish',
    ];

    $postID = wp_insert_post( $post );

    wp_set_object_terms( $postID, [ $request[ 'ticket_status' ] ], 'ticket_status' );
    wp_set_object_terms( $postID, [ $request[ 'ticket_priority' ] ], 'ticket_priority' );
    wp_set_object_terms( $postID, [ $request[ 'ticket_service' ] ], 'ticket_service' );
    wp_set_object_terms( $postID, [ 'open' ], 'ticket_status' );

    $error = '';
    $attachment = $_POST[ 'attachment' ];
    update_post_meta( $postID, 'yp_ticket_attachment', $attachment[ 'id' ] );
    // $attachment = \YP\FileUpload::uploadFile( $_FILES, 'attachment', $postID, true );

    $data = [
      'post' => $post,
      'postID' => $postID,
      'request' => $request,
      'error' => $error,
      'attachment' => $attachment,
    ];

    echo json_encode( $data );

    die();

  }

}
