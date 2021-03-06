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

    $attachmentID = (int) get_post_meta( $post->ID, 'yp_ticket_attachment', true );
    $buildingID = get_post_meta( $post->ID, 'yp_ticket_building', true );
    $notify = get_post_meta( $post->ID, 'yp_ticket_notify_author', true );

    $buildingName = ( !empty( $buildingID ) ) ? $buildingName = get_the_title( $buildingID ) : '';

    ?>

    <div class="yp-tickets__box">

      <div class="meta-options yp-tickets__field">
        <div class="field-row col-12 col-md-6">
          <label class="yp-tickets__label" for="yp_ticket_attachment">
            <strong>Building</strong><br />
            <em>Please note that this is <strong>not</strong> editable</em>
          </label>

          <input type="text" name="yp_ticket_building" disabled value="<?php echo $buildingName; ?>">

        </div><!-- .attachment -->

        <div class="field-row col-12 col-md-6">
          <label class="yp-tickets__label" for="yp_ticket_attachment">
            <strong>Attachment</strong>
          </label>

          <?php if ( is_numeric( $attachmentID ) ): ?>

            <div class="yp-ticket-attachment-details">

              <?php

                $arrAttachmentImg = wp_get_attachment_image_src( $attachmentID, 'full' );

                if ( $arrAttachmentImg ) {
                  $attachmentUrl = $arrAttachmentImg[0];
                }

              ?>

              <a href="<?php echo $attachmentUrl; ?>" target="_blank" title=""><img width="80%" src="<?php echo $attachmentUrl; ?>" /></a><br />
              <small><?php echo basename( $attachmentUrl ); ?></small>

            </div>

          <?php endif; ?>

          <input id="yp_ticket_attachment" type="hidden" name="yp_ticket_attachment" value="<?php echo $attachmentID; ?>" />

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

    // Make sure our flag is in there, otherwise it's an autosave and we should bail.
    if ( isset( $_POST[ 'yp_ticket_building' ] ) ) {
      update_post_meta( $postID, 'yp_ticket_building', $_POST[ 'yp_ticket_building' ] );
    }

  }

}
