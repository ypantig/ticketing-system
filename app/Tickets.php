<?php

namespace YP;

class Tickets {

  public function __construct() {

    add_action( 'save_post', [ &$this, 'save_post__onSaveTicket' ] );

  }

  public function save_post__onSaveTicket( $postID ) {

    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $postID ) ) {
      return;
    }

    $email = get_option( 'yp_ticket_notification_from_email' ) ? get_option( 'yp_ticket_notification_email' ) : get_option( 'admin_email' );

    $blogName = get_option( 'blogname' );
    $post = get_post( $postID );

    $postTitle = $post->post_title;
    $postUrl = get_permalink( $postID );
    $postContent = $post->post_content;
    $subject = 'New ticket from ' . $blogName . ': ' . $postTitle;

    $message = $postContent;
    $message .= $post_title . ": " . $postUrl;

    $headers = [
      ''
    ];

    // Send email to admin / email in notification
    wp_mail( $email, $subject, $message );

  }

}
