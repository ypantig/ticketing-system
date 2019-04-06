<?php

namespace YP;

class Notifications {

  public function __construct() {

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if ( !is_plugin_active( 'notification/notification.php' ) ) {
      return;
    }

    // Hook just after the Triggers are registered.
    add_action( 'notification/trigger/registered', [ &$this, 'notification__registerTicketAttachmentMergeTag' ] );
    add_action( 'notification/trigger/registered', [ &$this, 'notification__registerTicketMergeTagForComments' ] );

  }

  public function notification__registerTicketAttachmentMergeTag( $trigger ) {

    $allowedTrigger = [
      'wordpress/yp_ticket/added',
      'wordpress/yp_ticket/updated',
      'wordpress/yp_ticket/published',
    ];

    // Check if registered Trigger is the one we need.
    if ( !in_array( $trigger->get_slug(), $allowedTrigger ) ) {
      return;
    }

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_attachment',
      'name'        => __( 'Ticket Attachment', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $attachment = get_post_meta( $trigger->post->ID, '_yp_ticket_attachment', true );
        return wp_get_attachment_url( $attachment );
      },
    ) ) );

  }

  public function notification__registerTicketMergeTagForComments( $trigger ) {

    $allowedTrigger = [
      'wordpress/comment_comment_added',
      'wordpress/comment_comment_replied',
      'wordpress/comment_comment_approved',
      'wordpress/comment_comment_unapproved',
      'wordpress/comment_comment_spammed',
      'wordpress/comment_comment_trashed',
    ];

    // Check if registered Trigger is the one we need.
    if ( !in_array( $trigger->get_slug(), $allowedTrigger ) ) {
      return;
    }

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_attachment',
      'name'        => __( 'Ticket Attachment', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $attachment = get_post_meta( $trigger->post->ID, '_yp_ticket_attachment', true );
        return wp_get_attachment_url( $attachment );
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_title',
      'name'        => __( 'Ticket Title', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        return get_the_title( $trigger->post->ID );
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_ticket_status',
      'name'        => __( 'Ticket Status', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $status = get_the_terms( $trigger->post->ID, 'ticket_status' )[0]->name;
        return $status;
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_ticket_priority',
      'name'        => __( 'Ticket Priority', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $priority = get_the_terms( $trigger->post->ID, 'ticket_priority' )[0]->name;
        return $priority;
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_ticket_service',
      'name'        => __( 'Ticket Service', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $service = get_the_terms( $trigger->post->ID, 'ticket_service' )[0]->name;
        return $service;
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_permalink',
      'name'        => __( 'Ticket Permalink', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        return get_permalink( $trigger->post->ID );
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_author',
      'name'        => __( 'Ticket Author', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        $author = get_the_author_meta( 'display_name', $trigger->post->post_author );
        return $author;
      },
    ) ) );

    $trigger->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( array(
      'slug'        => 'yp_ticket_content',
      'name'        => __( 'Ticket Author', 'textdomain' ),
      'resolver'    => function( $trigger ) {
        return $trigger->post->post_content;
      },
    ) ) );

  }

}
