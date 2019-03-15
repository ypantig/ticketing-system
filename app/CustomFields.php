<?php

namespace YP;

class CustomFields {

  public function __construct() {

    add_action( 'add_meta_boxes', [ &$this, 'add_meta_boxes__addMetaBoxForCustomFields' ] );
    add_action( 'save_post', [ &$this, 'save_post__saveFieldData' ] );

    add_action( 'post_edit_form_tag', [ &$this, 'post_edit_form_tag__addEnctype' ] );

  }

  public function post_edit_form_tag__addEnctype() {
    echo ' enctype="multipart/form-data"';
  }

  /**
   * add meta box for custom fields
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function add_meta_boxes__addMetaBoxForCustomFields() {

    global $ticketPostType;

    add_meta_box(
      'tickets-information',
      'Information',
      [ &$this, 'ticketInformation' ],
      $ticketPostType,
      'normal',
      'high'
    );

  }

  /**
   * callback for adding meta boxes to hold custom fields
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function ticketInformation( $post ) {

    $attachmentID = get_post_meta( $post->ID, 'yp_ticket_attachment', true );
    $notify = get_post_meta( $post->ID, 'yp_ticket_notify_author', true );

    ?>

    <div class="yp-tickets__box">

      <div class="meta-options yp-tickets__field">
        <div class="field-row">
          <label class="yp-tickets__label" for="yp_ticket_attachment">
            <strong>Attachment</strong>
          </label>

          <?php if ( is_numeric( $attachmentID ) ): ?>

            <div class="yp-ticket-attachment-details">

              <?php

                $arrAttachmentImg = wp_get_attachment_image_src( $attachmentID, 'medium' );
                $attachmentImg = $arrAttachmentImg[0];

                if ( !$arrAttachmentImg ) {
                  $attachmentUrl = wp_get_attachment_url( $attachmentID );
                  $attachmentImg = includes_url( 'images/media/' . Utils::getFileImage( $attachmentUrl ) . '.png' );
                }

              ?>

              <img src="<?php echo $attachmentImg; ?>" /><br />
              <small><?php echo $attachmentUrl; ?></small>

            </div>

          <?php endif; ?>

          <input id="yp_ticket_attachment" type="file" name="yp_ticket_attachment" value="<?php echo $attachmentID; ?>" />

          <?php
            // Put in a hidden flag. This helps differentiate between manual saves and auto-saves (in auto-saves, the file wouldn't be passed).
          ?>

          <input type="hidden" name="yp_ticket_attachment_manual_save_flag" value="true" />
        </div><!-- .attachment -->

        <!-- <div class="field-row">
          <div class="yp-tickets__label">Notifications</div>
          <label for="yp_ticket_notify_author">
            <input type="checkbox" name="yp_ticket_notify_author" id="yp_ticket_notify_author" <?php //if ( $notify || $notify == 'on' ) : echo 'checked'; endif; ?> />
            Notify of new comments to this ticket.
          </label>
        </div> --><!-- .field-row -->
      </div>
    </div>

    <?php

  }

  public function save_post__saveFieldData( $postID ) {

    if ( !$postID ) {
      return;
    }

    // Make sure our flag is in there, otherwise it's an autosave and we should bail.
    update_post_meta( $postID, 'yp_ticket_notify_author', isset( $_POST[ 'yp_ticket_notify_author' ] ) );

    // Make sure our flag is in there, otherwise it's an autosave and we should bail.
    if ( isset( $_POST[ 'yp_ticket_attachment_manual_save_flag' ] ) ) {
      \YP\FileUpload::uploadFile( $_FILES, 'yp_ticket_attachment', $postID );
      update_post_meta( $postID, 'yp_ticket_attachment', $_POST[ 'yp_ticket_attachment' ] );
    }

  }

}
