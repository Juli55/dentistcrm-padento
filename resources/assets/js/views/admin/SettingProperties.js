import SettingProperties from './settingproperties.template.html';
import NewPropertyModal from '../../components/admin/NewPropertyModal.js';

export default {
  template: SettingProperties,
  components: {
    NewPropertyModal
  },
  data() {
    return {
      properties: [],
      sitename: 'Kontakteigenschaften'
    };
  },
  created: function() {
    this.getProps();
    $('#title').html(this.sitename);
    $('title').text(this.sitename + ' | Padento.de');

    // var resource = this.$resource('/api/settings/patient-props');
    // resource.get().then(function(response) {
    //   this.properties = response.data;
    // }.bind(this));
  },
  methods:{
    getProps: function () {
      var resource = this.$resource('/api/settings/patient-props');
      resource.get().then(function(response) {
        this.properties = response.data;
      }.bind(this));
    }
  }
};
