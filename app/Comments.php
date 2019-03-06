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

    // add_action( 'comment_form_logged_in', [ &$this, 'comment_form_logged_in__modifyCommentForm' ] );

    add_action( 'comment_post', [ &$this, 'comment_post__changeStatusIfNeeded' ], 10, 2 );

  }

  public function comment_post__changeStatusIfNeeded( $commentID, $comment ) {

    $postID = $_POST[ 'comment_post_ID' ];
    $currentStatus = get_the_terms( $postID, 'ticket_status' )[0]->slug;

    $formStatus = $_POST[ 'ticket_status' ];

    if ( $currentStatus != $formStatus ) {
      wp_set_object_terms( $postID, [ $_POST[ 'ticket_status' ] ], 'ticket_status' );
    }

  }

  public function comment_form_logged_in__modifyCommentForm( $postID ) {

    $userID = get_current_user_id();
    if ( !user_can( $userID, 'remove_users' ) ) {
      return;
    }

    $markup = '';
    $currentStatus = get_the_terms( $postID, 'ticket_status' )[0];

    $markup .= '<div class="row">';
    $markup .= $this->buildStatusField( $currentStatus );
    $markup .= '</div>';

    echo $markup;

  }

  public function comment_form_defaults__updateDefaultFields( $defaults ) {

    $usermeta = get_userdata( get_current_user_id() );
    $newDefaults = [
      'title_reply' => __( 'Additional Notes', 'yp-ticketing-system' ),
      'label_submit' => __( 'Submit', 'yp-ticketing-system' ),
      'class_submit' => 'btn btn--primary',
      'title_reply_before' => '<h3 id="reply-title" class="h4 comment-reply-title">',
      'comment_field' => '<div class="row">' . $this->buildStatusField( $currentStatus ) . '</div><div class="comment-form-comment"><label for="comment">' . _x( 'Notes', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="4" maxlength="65525" required="required"></textarea></div>',
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
            <em class="text-grey-opacity xxs"><?php echo get_comment_date( 'F j, Y g:mA', $comment ); ?></em>
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

  public function buildStatusField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_status' );

    $labels = [
      'label' => __( 'Status', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a status', 'yp-ticketing-system' ),
      'id' => 'ticket_status',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildPriorityField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_priority' );

    $labels = [
      'label' => __( 'Priority', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a priority', 'yp-ticketing-system' ),
      'id' => 'ticket_priority',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildServiceField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_service' );

    $labels = [
      'label' => __( 'Service', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a service', 'yp-ticketing-system' ),
      'id' => 'ticket_service',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildSelectField( $terms, $labels, $current = [] ) {

    $markup = '<div class="col-12 form-field col-md-6">
              <label for="' . $labels[ 'id' ] . '">' . $labels[ 'label' ] .'</label>
              <div class="select-field">
                <select name="' . $labels[ 'id' ] . '" id="' . $labels[ 'id' ] . '" required>
                  <option value="" disabled selected>' . $labels[ 'placeholder' ] . '</option>';

                  foreach ( $terms as $item ):
                    if ( !empty( $current ) && $current->slug == $item->slug ) {
                      $markup .= '<option value="' . $item->slug . '" selected>' . $item->name .'</option>';
                    } else {
                      $markup .= '<option value="' . $item->slug . '">' . $item->name .'</option>';
                    }
                  endforeach;

                $markup .= '</select>
              </div><!-- .select-field -->
            </div>';

    return $markup;

  }

  public function buildUploadField() {

    $markup = '<div class="col-12 form-field col-md-6">
              <label for="attachment">' . __( 'Upload file', 'yp-ticketing-system' ) .'</label>
              <div class="upload-field">
                <input type="attachment" name="attachment" />
                <button class="js-file-upload">Upload file</button>
                <span class="js-file-name"></span>
              </div><!-- .upload-field -->
            </div>';

    return $markup;

  }

}
