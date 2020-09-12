import AllLabs from './labs.template.html';
import store from '../../stores/ContactsStore.js';

export default {

  template: AllLabs,
  data () {
    return {
      shared: store,
      labs: [],
      sitename: 'Labore',
      stats: [],
      sortFilter: 'id',
      // whoami: [],
      filterOptions: {
          status: {
              options: [
                  { value: 'reset', text: 'Alle' },
                  { value: 'aktiv', text: 'Aktiv' },
                  { value: 'inaktiv', text: 'Inaktiv' },
              ],
          },
          membership: {
              options: [
                  { value: 'reset', text: 'Alle' },
                  { value: '0', text: 'Standardlabor' },
                  { value: '1', text: 'Kontaktverwaltung' },
                  // { value: '2', text: 'Mehr Budget' },
                  // { value: '4', text: 'Kontaktverwaltung und mehr Budget' },
                  { value: '5', text: 'Zahnarzt Labor' }
              ],
          },
      },
      filter: {
          status: {
              selected: 'reset',
          },
          membership: {
              selected: 'reset',
          },
          isCRM:false,
          isDentistCRM:false,
          //isMultipleUser:false

      },
      sortFilter: 'created_at',
      pagination: {
          total: 0,
          per_page: 100,
          from: 1,
          to: 0,
          current_page: 1,
      },
      offset: 4,
      items_per_page: [
          { value: 25 },
          { value: 50 },
          { value: 100 },
          { value: 150 },
          { value: 200 }
      ],
      orderby: {
          name: 'labs.created_at',
          sort: 'desc'
      },
      hostName: 'padento.de',
    };
  },
  created: function() {
    $('#title').html(this.sitename);
    $('title').text(this.sitename + ' | Padento.de');
    // this.whoAmI();
    new Clipboard('.clip');
  },
  ready: function() {
    if (store.labs.stored == true) {
        this.pagination.current_page = store.labs.data.page;
        this.orderby                 = store.labs.data.orderby;
        this.labs                    = store.labs.labs;
        this.stats                   = store.labs.stats;
        this.filter                  = store.labs.data.filter;
        // this.searchfor            = store.labs.data.searchfor;
        // this.search               = store.labs.data.searchfor;
        // $('#search').val(this.search);
    } else {
        this.fetchItems(this.pagination.current_page);
    }
  },
  watch: {
      'filter': {
          handler: function(value) {
              this.fetchItems(1, true, 'filter');
          },
          deep: true
      },
      search: function(value) {
          this.fetchItems(1, true, 'search');
      }
  },
  computed: {
      isActived: function () {
          return this.pagination.current_page;
      },
      pagesNumber: function () {
          if (!this.pagination.to) {
              return [];
          }
          var from = this.pagination.current_page - this.offset;
          if (from < 1) {
              from = 1;
          }
          var to = from + (this.offset * 2);
          if (to >= this.pagination.last_page) {
              to = this.pagination.last_page;
          }
          var pagesArray = [];
          while (from <= to) {
              pagesArray.push(from);
              from++;
          }
          return pagesArray;
      },
  },
  methods: {
    clip: function(event) {
      Messenger().post({
        message: 'Direktlink kopiert',
        type: 'success'
      });
    },
    sortBy: function(name) {
      if (this.orderby.sort == 'asc') {
        var sort = 'desc';
      } else {
        var sort = 'asc';
      }
      //console.log(this.orderby.sort);
      this.orderby = {name: name, sort: sort };
      this.fetchItems(1);
    },
    changePage: function (page) {
        this.pagination.current_page = page;
        this.fetchItems(page, true, 'changepage');
    },
    fetchItems: function(page, killCache = false, initby = null) {
      this.data = {
          page: page,
          pagination: this.pagination,
          orderby: this.orderby,
          filter: this.filter,
          searchfor: this.search
      };
      this.$http.get('/api/labs', this.data).then(function(response) {
          console.log(response.data);
          this.$set('labs', response.data.data.data);
          this.$set('pagination', response.data.pagination);
          this.$set('stats', response.data.stats);
          store.labs = {
              stored: true,
              data: this.data,
              labs: this.labs,
              stats: this.stats
          };
          document.getElementById('main-content').scrollTop = 0;
          // $('#spinner').removeClass('show');
          if (page > 1) {
              Messenger().post({
                  message: 'Seite ' + page + ' geladen',
                  type: 'success'
              });
          }

      }.bind(this), function(error) {
          $('#debug').addClass('active').find('.debugged-content').html(error.data);
      }).bind(this);

      this.debug = this.data;
    },
    whoAmI: function() {
        this.$http.get('/api/whoami')
            .then(response => {
                this.whoami = response.data;
            });
    },
  },
  filters: {
    'count': function(collection) {
      return collection.length;
    },
    'round': function(number) {
      return Math.round(number);
    },
    'translateMembership': function(membership) {
      if (membership == '0') return 'Standardlabor';
      if (membership == '1') return 'Kontaktverwaltung';
      if (membership == '2') return 'Mehr Budget';
      if (membership == '4') return 'Kontaktverwaltung und mehr Budget';
      if (membership == '5') return 'Zahnarzt Labor';
    },
  }
}





























































