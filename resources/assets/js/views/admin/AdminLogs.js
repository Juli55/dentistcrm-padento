import Logs from './admin-logs.template.html';

export default {
  template: Logs,
  data () {
    return {
      logs: [],
      sitename: 'Padento Log'
    };
  },
  created: function() {
    $.getJSON('/api/logs', function(data) {
      this.logs = data;
    }.bind(this));
    $('#title').html( this.sitename);
  }
}
