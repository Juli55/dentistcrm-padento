import DocsPage from './DocsPage.template.html';

export default {
  template: DocsPage,
  props: [''],
  data () {
    return {
        n: 0,
        sitename: 'Dokumentation'
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
