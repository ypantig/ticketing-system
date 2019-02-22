// import 'jquery-form';

export default {
  init() {

    this.submitTicket = this.submitTicket.bind( this );
    $( document ).on( 'submit', '.js-yp-ticket-form', this.submitTicket )
    $( document ).on( 'change', '.js-attachment', this.attachmentChange )

  },

  attachmentChange( event ) {

    event.preventDefault();
    const $form = $( event.currentTarget ).parents( 'form' );
    // const attachment = $form.find( '#attachment' ).prop( 'files' )[0];
    const file = $form.find( '#attachment' ).prop( 'files' )[0];

    const formData = new FormData();

    formData.append( 'action', 'upload-attachment' );
    formData.append( 'async-upload', file );
    formData.append( 'name', file.name );
    formData.append( '_wpnonce', window.yp_ticket_global.nonce );

    $.ajax({
      url: window.yp_ticket_global.upload_url,
      type: 'POST',
      contentType: false,
      processData: false,
      dataType: 'json',
      data: formData,
      error: ( error ) => {
        console.log( 'error' );
        console.log( error );
      },
      success: ( data ) => {
        console.log( data );
      },
    });

  },

  submitTicket( event ) {

    event.preventDefault();
    const $form = $( '.js-yp-ticket-form' );

    if ( $form.find( '#gotcha input' ).val() != '' ) {
      console.log( 'You\'re not allowed to submit this form, you robot.' );
      return;
    }

    const formData = this.jsonData( $form );

    console.log( formData );
    // console.log( attachment );

    $.ajax({
      url: window.yp_ticket_global.ajax_url,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'submit_ticket_form',
        data: formData,
      },
      error: ( error ) => {
        console.log( 'error' );
        console.log( error );
      },
      success: ( data ) => {
        console.log( data );
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
