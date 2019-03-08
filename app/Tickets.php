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

    $blogName = get_option( 'blogname' );
    $email = get_option( 'yp_ticket_notification_to_email' ) ? get_option( 'yp_ticket_notification_to_email' ) : get_option( 'admin_email' );
    $fromName = get_option( 'yp_ticket_notification_from_name' ) ? get_option( 'yp_ticket_notification_from_name' ) : $blogName;

    $post = get_post( $postID );

    $postTitle = $post->post_title;
    $postUrl = get_permalink( $postID );
    $postContent = $post->post_content;
    $subject = 'New ticket from ' . $blogName . ': ' . $postTitle;

    $message = $postContent;
    $message .= $post_title . ": " . $postUrl;

    $attachment = get_post_meta( $postID, 'yp_ticket_attachment', 1 );
    $attachments = [];

    if ( $attachment ) {
      $attachments[] = wp_get_attachment_url( $attachmentID );
    }

    $headers = [
      'From: ' . $fromName . '<' . $email . '>',
    ];

    // Send email to admin / email in notification
    wp_mail( $email, $subject, $message, $headers, $attachments );

  }

}
