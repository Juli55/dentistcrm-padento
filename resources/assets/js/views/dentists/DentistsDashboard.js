import AllDentists from './dentists.template.html';

export default {
  template: AllDentists,
  data () {
    return {
      dentists: [],
      sitename: 'Padento Zahnärzte',
      pagination: {
          total: 0,
          per_page: 200,
          from: 1,
          to: 0,
          current_page: 1
      },
      offset: 4,// left and right padding from the pagination <span>,just change it to see effects
      orderby: {
        name: 'created_at',
        sort: 'desc'
      },
    };
  },
  created: function() {
    this.fetchItems(this.pagination.current_page);
    $('#title').html( this.sitename);
  },
  watch: {
    'pagination.per_page': function(count) {
      // this.pagination.per_page = count;
      this.fetchItems(1);
    },
    search: function(value) {
      this.searchfor = {value: value};
      this.fetchItems(1);
    },
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
    filterBy: function(key, value, kill = false) {
      this.filterby = {key:key, value:value};
      if (kill == true) {
        this.filterby = {};
      }
      this.fetchItems(1);
    },
    changePage: function (page) {
        this.pagination.current_page = page;
        this.fetchItems(page);
    },
    fetchItems: function (page) {
       // this.page = page;
      var data = {
        page: page,
        pagination: this.pagination,
        orderby: this.orderby,
        filterby: this.filterby,
        searchfor: this.searchfor,
      };
      this.$http.get('/api/alldentists', data).then(function (response) {
          //look into the routes file and format your response
          //console.log(response.data);
          //console.log(response.data.data.data);
          this.$set('dentists', response.data.data.data);
          this.$set('pagination', response.data.pagination);
          // this.$set('stats', response.data.stats);
      }, function (error) {
          $('#debug').addClass('active').find('.debugged-content').html(error.data);
          //console.log(error.data);
          // $('#debug').addClass('active').find('.debugged-content').html(error.data);
          console.log(error.data);
      }).bind(this);
    },
  }
}
