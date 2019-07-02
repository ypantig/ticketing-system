<?php

namespace YP;

class Ajax {

  public function __construct() {

    add_action( 'wp_ajax_submit_ticket_form', [ &$this, 'wp_ajax_submitTicketForm' ] );
    add_action( 'wp_ajax_nopriv_submit_ticket_form', [ &$this, 'wp_ajax_submitTicketForm' ] );

    add_action( 'wp_ajax_upload_ticket_attachment', [ &$this, 'wp_ajax_uploadTicketAttachment' ] );
    add_action( 'wp_ajax_nopriv_upload_ticket_attachment', [ &$this, 'wp_ajax_uploadTicketAttachment' ] );

  }

  public function wp_ajax_uploadTicketAttachment() {

    $postID = isset( $_POST[ 'postID' ] ) ? (int) $_POST[ 'postID' ] : 0;
    $uploadDir = wp_upload_dir();
    $path = $uploadDir['path'] . '/';
    $maxUpload = 1;
    $count = 0;
    $attached = false;

    $attachments = get_posts( array(
      'post_type'         => 'attachment',
      'posts_per_page'    => -1,
      'post_parent'       => $postID,
      'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ));

    if( $_SERVER['REQUEST_METHOD'] == "POST" ) {

      // Check if user is trying to upload more than the allowed number of images for the current post
      if( ( count( $attachments ) + count( $_FILES['files']['name'] ) ) > $maxUpload ) {
        $upload_message[] = "Sorry you can only upload " . $maxUpload . " files.";
      } else {

        foreach ( $_FILES['files']['name'] as $f => $name ) {
          $extension = pathinfo( $name, PATHINFO_EXTENSION );

          if ( $_FILES['files']['error'][$f] == 4 ) {
            continue;
          }

          if ( $_FILES['files']['error'][$f] == 0 ) {
            // If no errors, upload the file...
            if( move_uploaded_file( $_FILES['files']['tmp_name'][$f], $path.$name ) ) {

              $count++;

              $filename = $path . $name;
              $filetype = wp_check_filetype( basename( $filename ), null );
              $attachment = array(
                'guid'           => $uploadDir['url'] . '/' . basename( $filename ),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
              );

              // Insert attachment to the database
              $attachmentID = wp_insert_attachment( $attachment, $filename, $postID );
              $attached = true;

              // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
              require_once( ABSPATH . 'wp-admin/includes/image.php' );

              // Generate the metadata for the attachment, and update the database record.
              $attachmentData = wp_generate_attachment_metadata( $attachmentID, $filename );
              wp_update_attachment_metadata( $attachmentID, $attachmentData );

              update_post_meta( $postID, 'yp_ticket_attachment', $attachmentID );

            }
          }
        }
      }
    }

    $messages = '';
    // Loop through each error then output it to the screen
    if ( isset( $upload_message ) ) {
      foreach ( $upload_message as $msg ) {
        $messages .= sprintf( __('%s', 'wp-trade'), $msg );
      }
    }

    // If no error, show success message
    if( $count != 0 ){
      $messages = sprintf( __('%d file(s) added successfully!', 'wp-trade'), $count );
    }

    echo json_encode([
      'message' => $messages,
      'success' => $attached,
    ]);

    die();

  }

  public function wp_ajax_submitTicketForm() {

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
      'comment_status' => 'open',
    ];

    $postID = wp_insert_post( $post );

    wp_set_object_terms( $postID, [ $request[ 'ticket_status' ] ], 'ticket_status' );
    wp_set_object_terms( $postID, [ $request[ 'ticket_priority' ] ], 'ticket_priority' );
    wp_set_object_terms( $postID, [ $request[ 'ticket_service' ] ], 'ticket_service' );
    wp_set_object_terms( $postID, [ 'open' ], 'ticket_status' );

    // $error = '';
    // $attachment = $_POST[ 'attachment' ];
    // update_post_meta( $postID, 'yp_ticket_attachment', $attachment[ 'id' ] );

    update_post_meta( $postID, 'new_ticket_admin', '1' );
    update_post_meta( $postID, 'new_ticket_author', '0' );
    update_post_meta( $postID, 'is_new_ticket', '1' );
    update_post_meta( $postID, 'is_commented_ticket', '0' );

    update_post_meta( $postID, 'yp_ticket_building', $request[ 'ticket_building' ] );

    Tickets::updateAdminTicketCount( 'increment', $postID );

    $data = [
      'post' => $post,
      'postID' => $postID,
      'request' => $request,
    ];

    echo json_encode( $data );

    die();

  }

}
