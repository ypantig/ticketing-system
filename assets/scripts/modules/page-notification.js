export default {
  init() {

    const listPage = window.yp_ticket_global.settings.page_list;
    const newTicketCount = parseInt( window.yp_ticket_global.new_ticket_count );

    if ( newTicketCount > 0 ) {
      $( '.js-menu-item-' + listPage + ' > a' ).addClass( 'has-notification-badge' ).append( '<span class="link__notification js-notification">' + newTicketCount + '</span>' );
    }

  },
}
