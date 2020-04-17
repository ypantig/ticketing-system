<?php

namespace YP;

class Comments {

  public function __construct() {

    // unset fields that are not needed
    // add_filter( 'comment_form_field_url', '__return_false' );
    // // add_filter( 'comment_form_field_email', '__return_false' );
    // add_filter( 'comment_form_field_website', '__return_false' );
    // add_filter( 'comment_form_field_author', '__return_false' );

    add_filter( 'comment_form_defaults', [ &$this, 'comment_form_defaults__updateDefaultFields' ] );

    add_filter( 'comments_template', [ &$this, 'comments_template__updateCommentTemplate' ] );

    add_action( 'comment_post', [ &$this, 'comment_post__changeStatusIfNeeded' ], 10, 2 );
    add_action( 'comment_post', [ &$this, 'comment_post__changeTicketNotifier' ], 10, 2 );

    // add_action( 'init', [ &$this, 'init__updateCommentStatus' ] );

  }

  public function init__updateCommentStatus() {

    global $ticketPostType;
    $args = array(
      'posts_per_page' => -1,
      'post_type' => $ticketPostType,
    );

    $query = new \WP_Query( $args );

    if( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();

      $ticketStatus = get_the_terms( $query->post->ID, 'ticket_status' )[0]->slug;

      if ( $ticketStatus == 'open' ) {
        wp_update_post([
          'comment_status' => 'open',
        ]);
      }

    endwhile; wp_reset_postdata(); endif;

  }

  public function comment_post__changeStatusIfNeeded( $commentID, $comment ) {

    $postID = $_POST[ 'comment_post_ID' ];
    $currentStatus = get_the_terms( $postID, 'ticket_status' )[0]->slug;

    $formStatus = $_POST[ 'ticket_status' ];

    if ( $currentStatus != $formStatus ) {
      wp_set_object_terms( $postID, [ $_POST[ 'ticket_status' ] ], 'ticket_status' );
    }

  }

  /**
   * if user / admin posted a comment on the ticket,
   * make sure to reset the post_meta to true for user / admin
   * to display the notification
   *
   * @author Ynah Pantig <me@ynahpantig.com>
   */
  public function comment_post__changeTicketNotifier( $commentID, $comment ) {

    $post = get_post( $_POST[ 'comment_post_ID' ] );
    $currentUser = wp_get_current_user();

    update_post_meta( $post->ID, 'is_new_ticket', '0' );
    update_post_meta( $post->ID, 'is_commented_ticket', '1' );

    // since user posted the comment,
    // reset the value for the admin
    if ( $currentUser->ID == $post->post_author ) {
      update_post_meta( $post->ID, 'new_ticket_admin', '1' );
      Tickets::updateAdminTicketCount( 'increment', $post->ID );
    }

    // since the admin posted the comment,
    // reset the value for the author
    if ( Users::allowedAdminUser( $currentUser->roles ) ) {
      update_post_meta( $post->ID, 'new_ticket_author', '1' );
      Tickets::updateUserTicketCount( 'increment', $post->post_author, $post->ID );
    }

  }

  public function comment_form_defaults__updateDefaultFields( $defaults ) {

    $form = new Form();
    $usermeta = get_userdata( get_current_user_id() );
    $newDefaults = [
      'title_reply' => __( 'Additional Notes', 'yp-ticketing-system' ),
      'label_submit' => __( 'Submit', 'yp-ticketing-system' ),
      'class_submit' => 'btn btn--primary',
      'title_reply_before' => '<h3 id="reply-title" class="h4 comment-reply-title">',
      'comment_field' => $form->buildStatusField( $currentStatus ) . '<div class="comment-form-comment mt-3"><label for="comment">' . _x( 'Notes', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="4" maxlength="65525" required="required"></textarea></div>',
      'logged_in_as' => '',
    ];

    $defaults = array_merge( $defaults, $newDefaults );

    return $defaults;

  }

  public function comments_template__updateCommentTemplate( $template ) {

    global $post, $ticketPostType;

    if ( $post->post_type == $ticketPostType ) {
      $template = dirname( dirname( __FILE__ ) ) . '/assets/views/comments.php';
    }

    return $template;

  }

  public function wp_list_comments__callback( $comment, $args, $depth ) {

    if ( 'div' === $args['style'] ) {
      $tag       = 'div';
      $add_below = 'comment';
    } else {
      $tag       = 'li';
      $add_below = 'div-comment';
    }

    ?>

      <<?php echo $tag; ?> <?php comment_class( 'comment__item ' . ( empty( $args['has_children'] ) ? '' : 'parent' ) ); ?> id="comment-<?php comment_ID() ?>">
        <?php if ( 'div' != $args['style'] ): ?>
          <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
        <?php endif; ?>

        <div class="comment-author vcard">

          <?php if ( $args['avatar_size'] != 0 ): ?>
            <div class="author__image">
              <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
            </div><!-- .author__image -->
          <?php endif; ?>

          <small>
            <?php echo get_comment_author( $comment ); ?>
            <em class="text-grey-opacity xxs d-block"><?php echo get_comment_date( 'F j, Y g:mA', $comment ); ?></em>
          </small>

        </div>

        <?php if ( $comment->comment_approved == '0' ): ?>
          <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em><br/>
        <?php endif; ?>

        <?php comment_text(); ?>

        <div class="reply">
          <?php
            comment_reply_link(
              array_merge(
                $args,
                [
                  'add_below' => $add_below,
                  'depth'     => $depth,
                  'max_depth' => $args['max_depth']
                ]
              )
            );
          ?>
        </div>

      <?php if ( 'div' != $args['style'] ) : ?>
        </div>
      <?php endif; ?>

    <?php

  }


}
