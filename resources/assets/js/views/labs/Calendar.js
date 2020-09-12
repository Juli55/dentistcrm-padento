import CalendarView from './calendar.template.html';

export default {
  template: CalendarView,
  data () {
    return {
      timeFrames: [],
      labs: [],
      whoami: [],
      sitename: 'Meine Termine'
    };
  },
  created: function() {
    $('#title').html( this.sitename);
    //console.log('ready');
  },
  methods: {
    refresh: function() {
      $.getJSON('/api/labs', function(data) {
        this.labs = data;
      }.bind(this));
    }
  }
}
