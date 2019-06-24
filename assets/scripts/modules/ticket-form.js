export default {
  init() {

    this.triggerSubmit = this.triggerSubmit.bind( this );
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

      $( event.currentTarget ).siblings( '.js-file-name' ).html( filename );
    }

  },

  triggerUploadField( event ) {

    $( event.currentTarget ).siblings( '.js-attachment' ).click();

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

    if ( $form.find( '.js-attachment' ).val() != '' ) {

      const file = $form.find( '.js-attachment' ).prop( 'files' )[0];
      const formData = new FormData();

      formData.append( 'action', 'upload-attachment' );
      formData.append( 'async-upload', file );
      formData.append( 'name', file.name );
      formData.append( '_wpnonce', window.yp_ticket_global.nonce );

      const submitLabel = $form.find( '.js-submit-ticket' ).val();
      $form.find( '.js-submit-ticket' ).attr( 'disabled', 'disabled' ).val( 'Submitting Ticket...' );

      $.ajax({
        url: window.yp_ticket_global.upload_url,
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

            $form.find( '.js-yp-upload-error' ).html( data.data.message ).show();
            $form.find( '.js-submit-ticket' ).removeAttr( 'disabled' ).val( submitLabel );

          } else {

            self.submitTicketEvent( $form, data );

          }
        },
      });

    } else {

      self.submitTicketEvent( $form );

    }

  },

  submitTicketEvent( $form, data ) {

    const ticketData = this.jsonData( $form );

    $.ajax({
      url: window.yp_ticket_global.ajax_url,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'submit_ticket_form',
        data: ticketData,
        attachment: ( data && data.data ) ? data.data : null,
      },
      error: () => {
        // console.log( error );
        $form.find( '.js-yp-ticket-error' ).slideDown();
      },
      success: () => {
        // console.log( success );
        $form.find( '.js-yp-ticket-form-inner' ).slideUp();
        $form.find( '.js-yp-ticket-success' ).slideDown();

        $( 'html, body' ).animate({
          scrollTop: 0,
        });
      },
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
