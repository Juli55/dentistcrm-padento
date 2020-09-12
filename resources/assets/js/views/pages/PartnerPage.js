import PartnerPage from './PartnerPage.template.html';

export default {
  template: PartnerPage,
  props: [],
  data () {
    return {
        sitename: 'Partner'
    };
  },
  created () {
    $('#title').html(this.sitename);
    $('title').text(this.sitename + ' | Padento.de');
  },
  ready() {

  },
  computed: {
  },
  methods: {
  },
  filters: {
  }
};
