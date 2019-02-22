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

    // $post = [
    //   'post_title' => $request[ 'post_title' ],
    //   'post_content' => $request[ 'post_content' ],
    //   'post_author' => $request[ 'post_author' ],
    //   'post_type' => 'yp_ticket',
    //   'post_status' => 'publish',
    // ];

    // $postID = wp_insert_post( $post );

    // wp_set_object_terms( $postID, [ $request[ 'ticket_status' ] ], 'ticket_status' );
    // wp_set_object_terms( $postID, [ $request[ 'ticket_priority' ] ], 'ticket_priority' );
    // wp_set_object_terms( $postID, [ $request[ 'ticket_service' ] ], 'ticket_service' );
    // wp_set_object_terms( $postID, [ 'open' ], 'ticket_status' );

    // $uploadOverrides = [
    //   'test_form' => false
    // ];

    // if( !function_exists( 'wp_handle_upload' ) ){
    //   require_once( ABSPATH . 'wp-admin/includes/file.php' );
    // }

    // $fileReturn = wp_handle_upload( $request[ 'attachment' ], $uploadOverrides );

    // if( !isset( $fileReturn[ 'error' ] ) && !isset( $fileReturn[ 'upload_error_handler' ] ) ) {
    //   $filename = $fileReturn['file'];

    //   $attachment = array(
    //     'post_mime_type' => $fileReturn['type'],
    //     'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
    //     'post_content' => '',
    //     'post_status' => 'inherit',
    //     'guid' => $fileReturn['url']
    //   );

    //   $attachmentID = wp_insert_attachment( $attachment, $fileReturn['url'] );

    //   require_once(ABSPATH . 'wp-admin/includes/image.php');
    //   $attachmentData = wp_generate_attachment_metadata( $attachmentID, $filename );
    //   wp_update_attachment_metadata( $attachmentID, $attachmentData );

    //   if ( 0 < intval( $attachmentID ) ) {
    //     update_post_meta( $postID, 'yp_comment_attachment', $attachment );
    //     // wp_insert_attachment( $attachment, $fileReturn[ 'url' ], $postID );
    //   }
    // }

    $data = [
      'post' => $post,
      'postID' => $postID,
      'request' => $request,
      // 'attachment' => $attachmentID,
      // 'file' => $fileReturn,
    ];

    echo json_encode( $data );

    die();

  }

}
