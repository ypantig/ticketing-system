<?php

namespace YP;

class Tickets {

  public function __construct() {

    // add_action( 'save_post', [ &$this, 'save_post__onSaveTicket' ] );
    add_action( 'wp', [ &$this, 'wp__checkIfNewTicket' ] );

  }

  public function getAllTickets( $request ) {

    $sites = get_sites([
      'site__not_in' => [ 1 ],
    ]);

    $tickets = [];

    foreach ( $sites as $site ) {

      $tickets[ $site->blog_id ] = self::getSiteTicket([ 'id' => $site->blog_id ]);
      // switch_to_blog( $site->blog_id );
      // $args = array(
      //   'posts_per_page' => -1,
      //   'post_type' => 'yp_ticket',
      // );

      // $query = new \WP_Query( $args );
      // if ( $query->found_posts > 0 ) {
      //   $tickets[ $site->blog_id ] = $query->posts;
      // }

      // restore_current_blog();

    }

    return $tickets;

  }

  public function getSiteTicket( $data ) {

    $tickets = [];

    switch_to_blog( $data[ 'id' ] );
    $args = array(
      'posts_per_page' => -1,
      'post_type' => 'yp_ticket',
    );

    $query = new \WP_Query( $args );
    if ( $query->found_posts > 0 ) {
      $tickets = $query->posts;
    }

    restore_current_blog();

    return $tickets;

  }

  public function wp__checkIfNewTicket() {

    global $ticketPostType, $post;

    if ( is_admin() ) {
      return;
    }

    // check if single ticket
    if ( $post->post_type != $ticketPostType ) {
      return;
    }

    $authorID = $post->post_author;
    $currentUser = wp_get_current_user();

    if ( $currentUser->ID == $authorID ) {
      update_post_meta( $post->ID, 'new_ticket_author', '0' );
      Tickets::updateUserTicketCount( 'decrement', $currentUser, $post->ID );
    }

    if ( Users::allowedAdminUser( $currentUser->roles ) ) {
      update_post_meta( $post->ID, 'new_ticket_admin', '0' );
      Tickets::updateUserTicketCount( 'decrement', $currentUser, $post->ID );
    }

  }

  public static function updateUserTicketCount( $type = 'increment', $user = '', $postID = '' ) {

    if ( !empty( $user ) ) {
      if ( is_object( $user ) ) {
        $currentUser = $user;
      } else {
        $currentUser = new \WP_User( $user );
      }
    } else {
      $currentUser = wp_get_current_user();
    }

    $newTickets = get_user_meta( $currentUser->ID, 'yp_new_tickets' );

    if ( !empty( $newTickets ) ) {
      $newTickets = get_user_meta( $currentUser->ID, 'yp_new_tickets', 1 );
    }

    if ( isset( $newTickets[ $postID ] ) ) {
      $newTickets[ $postID ] = static::updateTicketNum( $newTickets[ $postID ], $type );
    } else {
      $newTickets[ $postID ] = static::updateTicketNum( 0, $type );
    }

    update_user_meta( $currentUser->ID, 'yp_new_tickets', $newTickets );

  }

  public static function updateAdminTicketCount( $type = 'increment', $postID = '' ) {

    global $ticketPostType;

    $admins = Users::getAdmins();

    foreach ( $admins as $admin ) {
      $newTickets = get_user_meta( $admin->ID, 'yp_new_tickets' );
      if ( !empty( $newTickets ) ) {
        $newTickets = get_user_meta( $admin->ID, 'yp_new_tickets', 1 );
      }

      if ( isset( $newTickets[ $postID ] ) ) {
        $newTickets[ $postID ] = static::updateTicketNum( $newTickets[ $postID ], $type );
      } else {
        $newTickets[ $postID ] = static::updateTicketNum( 0, $type );
      }

      update_user_meta( $admin->ID, 'yp_new_tickets', $newTickets );
    }

  }

  private static function updateTicketNum( $oldNum, $type = 'increment' ) {

    $oldNum = (int) $oldNum;

    if ( $type == 'increment' ) {
      $new = $oldNum + 1;
    } else {
      $new = 0;
    }

    return $new;
  }

}
