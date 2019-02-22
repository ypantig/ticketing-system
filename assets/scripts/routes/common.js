import ticketForm from './../modules/ticket-form.js';
import archive from './../modules/archive.js';

export default {
  init() {

    ticketForm.init();
    archive.init();

  },
  finalize() {

    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
