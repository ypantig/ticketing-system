import Vue from 'vue';
import Tickets from './../vue/Tickets/Tickets.vue';
import InfiniteLoading from 'vue-infinite-loading';

export default {

  init() {

    if ( document.querySelector( '.js-archive-container' ) == null ) {
      return;
    }

    this.els = {
      'tickets': {
        el: '#tickets',
        components: {
          Tickets,
        },
        render: h => h( Tickets ),
        template: "<ticket-list />",
      },
    }

    this.onReady = this.onReady.bind( this );

    window.addEventListener( 'DOMContentLoaded', this.onReady );

  },

  onReady() {

    this.loadArchive();

  },

  loadArchive() {

    Object.keys( this.els ).map( id => {

      if ( document.getElementById( id ) != null ) {
        new Vue( this.els[ id ] );

        if ( document.querySelector( '.js-load-more-pagination' ) != null ) {
          Vue.use( InfiniteLoading );
        }
      }

    });

  },

}
