export default {
  init() {

    this.triggerSubmit = this.triggerSubmit.bind( this );
    this.uploadAttachment = this.uploadAttachment.bind( this );
    $( document ).on( 'submit', '.js-yp-ticket-form', this.triggerSubmit );
    $( document ).on( 'click', '.js-file-upload', this.triggerUploadField );
    $( document ).on( 'change', '.js-attachment', this.changeUploadField );

  },

  changeUploadField( event ) {

    const fullPath = event.currentTarget.value;

    if ( fullPath ) {
      const startIndex = ( fullPath.indexOf( '\\' ) >= 0 ? fullPath.lastIndexOf( '\\' ) : fullPath.lastIndexOf( '/' ) );
      let filename = fullPath.substring( startIndex );

      if (filename.indexOf( '\\' ) === 0 || filename.indexOf( '/' ) === 0) {
        filename = filename.substring(1);
      }

      $( event.currentTarget ).parents( '.js-ticket-upload-field' ).find( '.js-file-name' ).html( filename );
    }

  },

  triggerUploadField( event ) {

    $( event.currentTarget ).parents( '.js-ticket-upload-field' ).find( '.js-attachment' ).click();

  },

  triggerSubmit( event ) {

    event.preventDefault();
    const $form = $( '.js-yp-ticket-form' );

    if ( $form.find( '#gotcha input' ).val() != '' ) {
      // console.log( 'You\'re not allowed to submit this form, you robot.' );
      return;
    }

    $form.find( '.js-yp-upload-error' ).hide();
    $form.find( '.js-yp-ticket-error' ).slideUp();

    const self = this;
    const ticketData = this.jsonData( $form );

    $.ajax({
      url: window.yp_ticket_global.ajax_url,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'submit_ticket_form',
        data: ticketData,
      },
      error: () => {
        $form.find( '.js-yp-ticket-error' ).slideDown();
      },
      success: ( data ) => {
        if ( $form.find( '.js-attachment' ).val() != '' ) {
          self.uploadAttachment( $form, data.postID );
        } else {
          this.toggleFormSuccessMessage( $form );
        }
      },
    });

  },

  uploadAttachment( $form, postID ) {

    const files = $form.find( '.js-attachment' )[0].files;
    const formData = new FormData();

    formData.append( 'action', 'upload_ticket_attachment' );
    formData.append( 'files[0]', files[0] );
    formData.append( 'postID', postID );

    const submitLabel = $form.find( '.js-submit-ticket' ).val();
    $form.find( '.js-submit-ticket' ).attr( 'disabled', 'disabled' ).val( 'Submitting Ticket...' );

    $.ajax({
      url: window.yp_ticket_global.ajax_url,
      type: 'POST',
      contentType: false,
      processData: false,
      dataType: 'json',
      data: formData,
      error: ( error ) => {
        $form.find( '.js-yp-upload-error' ).html( error ).show();
      },
      success: ( data ) => {
        if ( !data.success ) {
          $form.find( '.js-yp-upload-error' ).html( data.message ).show();
          $form.find( '.js-submit-ticket' ).removeAttr( 'disabled' ).val( submitLabel );
        } else {
          this.toggleFormSuccessMessage( $form );
        }
      },
    });

  },

  toggleFormSuccessMessage( $form ) {

    $form.find( '.js-yp-ticket-form-inner' ).slideUp();
    $form.find( '.js-yp-ticket-success' ).slideDown();

    $( 'html, body' ).animate({
      scrollTop: 0,
    });

  },

  jsonData( $form ) {
    const arrData = $form.serializeArray();
    let data = {};

    $.each( arrData, ( index, elem ) => {
      data[ elem.name ] = elem.value ;
    });

    return data;
  },
}
