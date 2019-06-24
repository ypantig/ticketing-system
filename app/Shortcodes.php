<?php

namespace YP;

class Shortcodes {

  public function __construct() {

    add_shortcode( 'yp-submit-tickets', [ &$this, 'add_shortcode__ypSubmitTickets' ] );
    add_shortcode( 'yp-tickets', [ &$this, 'add_shortcode__ypTickets' ] );

  }

  public function add_shortcode__ypTickets() {

    // get the current user
    global $ticketPostType;

    $args = array(
      'posts_per_page' => -1,
      'post_type' => $ticketPostType,
      'post_author' => get_current_user_id(),
    );

    $query = new \WP_Query( $args );

    ob_start();

    ?>

    <div class="js-archive-container">
      <div id="tickets"></div>
    </div>

    <?php

    $markup = ob_get_clean();

    return $markup;

  }

  /**
   * create a shortcode to display the form for
   * the ticketing system
   *
   * @author Ynah Pantig <ynah@ynahpantig.com>
   */
  public function add_shortcode__ypSubmitTickets() {

    // get the current user
    $currentUser = $GLOBALS['current_user'];

    $priorityTerms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_priority' );
    $serviceTerms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_service' );
    $buildings = Users::getMemberBuildings( $currentUser->ID );

    $markup = '';

    $formClass = new \YP\Form();

    ob_start();

    ?>

    <form action="" id="yp-tickets__form" class="yp-tickets__form js-yp-ticket-form" enctype="multipart/form-data">
      <div class="yp-tickets__messages">
        <div class="yp-ticket__message yp-tickets__success js-yp-ticket-success"><?php echo get_option( 'yp_ticket_success_message' ); ?></div>
        <div class="yp-ticket__message yp-tickets__error js-yp-ticket-error"><?php echo get_option( 'yp_ticket_error_message' ); ?></div>
      </div>

      <div class="yp-tickets__form-inner js-yp-ticket-form-inner">
        <input type="hidden" name="ID" value="" />
        <input type="hidden" name="post_author" value="<?php echo $currentUser->ID; ?>" />

        <div class="row">
          <div class="col-12 form-field">
            <?php echo $formClass->buildBuildingField(); ?>
          </div>

          <div class="col-12 form-field col-md-6">
            <?php echo $formClass->buildUserField( $currentUser ); ?>
          </div>

          <div class="col-12 form-field col-md-6">
            <?php echo $formClass->buildRoleField( $currentUser ); ?>
          </div>

          <div class="col-12 form-field col-md-6">
            <?php echo $formClass->buildServiceField(); ?>
          </div>

          <div class="col-12 form-field">
            <label for="post_title">
              <?php echo __( 'Subject', 'yp-ticketing-system' ); ?>
            </label>
            <input type="text" class="yp-tickets__field" name="post_title" placeholder="<?php echo __( '(ie. Elevator is broken)' ); ?>" required />
          </div>

          <div class="col-12 form-field">
            <label for="post_content">
              <?php echo __( 'Additional Notes', 'yp-ticketing-system' ); ?>
            </label>
            <textarea name="post_content" rows="6"></textarea>
          </div>

          <div class="col-12 form-field col-md-6">
            <?php echo $formClass->buildPriorityField(); ?>
          </div>

          <div class="col-12 form-field col-md-6">
            <label for="attachment"><?php echo __( 'Upload file', 'yp-ticketing-system' ); ?></label>
            <div class="yp-ticket-upload-field">
              <input type="file" name="async-upload" id="attachment" class="js-attachment sr-only" />
              <div class="yp-ticket-upload-error js-yp-upload-error small"></div>
              <button type="button" class="js-file-upload btn btn--secondary">Upload file</button>
              <span class="js-file-name yp-ticket-upload-filename"></span>
            </div>
          </div>

          <div class="col-12 form-field">
            <div class="form-group d-none" id="gotcha">
              <label>Leave this field empty</label>
              <div class="input-group">
                <input name="gotcha" class="form-control" type="text">
              </div>
            </div>

            <?php wp_nonce_field( 'yp-ticket-system' ); ?>
            <input type="submit" class="btn btn--primary js-submit-ticket" value="<?php echo __( 'Submit Ticket' ); ?>" />
          </div>
        </div>
      </div>
    </form>

  <?php

    $markup = ob_get_clean();

    return $markup;

  }

}
