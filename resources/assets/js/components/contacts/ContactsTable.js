import ContactsTable from './ContactsTable.template.html';

export default {
  template: ContactsTable,
  props: ['contacts'],
  data () {
    return {
        n: 0,
        active: 0,
        msg: ' <-- Datum',
        input: 'old',
    };
  },
  created () {
    this.fetchtest();
  },
  ready() {

  },
  computed: {
    example: {
      cache: true,
      get: function () {
        return Date.now() + this.msg;
      }
    },
    test: {
      cache: true,
      get: function (value) {
        // return Date.now() + this.msg;
        return this.input;
      }
    },
    msg: function () {
      return this.setmsg;
    }
  },
  methods: {
    fetchtest: function () {
      this.msg = 'YEAH';
    }
  },
  filters: {
    'count': function (arr) {
      // return arr.length;
      return arr.length;
    },
    'reverse': function (value) {
      return 'moep' + value + 'moep';
    },
  }
};
