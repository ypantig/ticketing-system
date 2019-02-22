<template>
  <div id="tickets" class="width-full">
    <div class="row align-items-center mb-3 mb-md-5 mt-3" animate-left>
      <div class="col-12 col-md-4" v-for="tax in filters">
        <div class="select-field form-group mb-3 mb-md-0">
          <select :name="tax.slug" v-model="tax.current" @change="filter" :data-select="tax.slug">
            <option value="disabled" selected disabled v-html="tax.placeholder"></option>
            <option value="all">All</option>
            <option v-for="term in tax.terms" :value="term.slug" v-html="term.name"></option>
          </select>
        </div>
      </div><!-- .col-12 col-md-6 -->
    </div><!-- .row -->

    <div class="row" v-if="searching">
      <div class="col-12 text-center filters__searching">
        <div class="spinner">
          <div class="bounce1"></div>
          <div class="bounce2"></div>
          <div class="bounce3"></div>
        </div>

        <div class="searching-text sm">Searching...</div>
      </div><!-- .filters__searching -->
    </div><!-- .row -->

    <div class="row" v-else>
      <div class="col-12" v-for="post in posts">
        <a :href="post.permalink" class="d-flex list__item no-gutters">
          <div class="col-12 col-md-8">
            <span class="h4 d-block" v-html="post.post_title"></span>
            <span class="text-grey-opacity" v-html="post.custom.excerpt"></span>
          </div>

          <div class="col-12 col-md-4 text-right">
            <div v-for="term in post.terms">
              <strong><span v-html="term.taxonomy.labels.singular_name"></span>:</strong> <span v-html="term.name"></span>
            </div>
          </div>
        </a>
      </div><!-- .col-12 -->

      <div class="col-12" v-if="noResults">{{ noResults }}</div><!-- .col-12 -->
    </div><!-- .row -->
  </div>
</template>

<script>

// import animation from '../../modules/animation.js';

export default {
  name: "Tickets",
  data() {
    return {
      posts: [],
      filters: {
        status: {
          terms: [],
          current: 'disabled',
          label: '',
          slug: 'status',
          placeholder: 'Filter by Status',
        },
        priority: {
          terms: [],
          current: 'disabled',
          label: '',
          slug: 'priority',
          placeholder: 'Filter by Priority',
        },
        service: {
          terms: [],
          current: 'disabled',
          label: '',
          slug: 'service',
          placeholder: 'Filter by Service',
        },
      },
      searching: false,
      noResults: false,
      page: 1,
    }
  },
  mounted() {

    wp.api.loadPromise.done( () => {
      this.loadArchive();
    });

  },
  watch: {
    priority: 'filter',
    status: 'filter',
    service: 'filter',
  },
  methods: {

    loadArchive() {

      const filtersData = {};
      const postData = {};

      this.buildFilters( filtersData );
      this.buildPosts( postData );

    },

    filter() {

      this.searching = true;
      this.page = 1;
      this.posts = [];

      const postData = {
        filters: {
          'priority': this.filters.priority.current,
          'service': this.filters.service.current,
          'status': this.filters.status.current,
        },
      };

      this.buildPosts( postData );

    },

    buildPosts( data ) {

      const allPosts = new wp.api.collections.Yp_ticket();
        console.log( this.searching );

      allPosts.fetch({ data }).done( ( results ) => {

        console.log( results );
        this.searching = false;

        if ( !results ) {
          this.noResults = results.no_results;
          return;
        }

        if ( ( typeof results.results == 'undefined' || Object.keys( results.results ).size == 0 ) ) {
          this.noResults = results.no_results;
          return;
        } else {
          this.noResults = false;
        }

        this.posts = results.results;

      })

    },

    buildFilters( data ) {

      const filters = {
        priority: new wp.api.collections.Ticket_priority(),
        status: new wp.api.collections.Ticket_status(),
        service: new wp.api.collections.Ticket_service(),
      };

      Object.keys( filters ).map( index => {
        const filterData = filters[ index ];

        filterData.fetch({ data }).done( ( results ) => {
          const fieldType = this.filters[ index ].field_type;
          const current = this.filters[ index ].current;

          this.filters[ index ] = results;
          this.filters[ index ].field_type = fieldType;
          this.filters[ index ].current = current;
        });
      })

    },
  },
}

</script>

<link rel="stylesheet" type="text/css" href="./career.css">
