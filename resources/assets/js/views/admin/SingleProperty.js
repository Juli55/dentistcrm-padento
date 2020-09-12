import SinglePropertyView from './single-settingproperty.template.html';

export default {
  template: SinglePropertyView,
  data() {
    return {
      property: []
    };
  },
  created: function() {
    var id = this.$route.params.id;
    var resource = this.$resource('/api/settings/single-property{/id}');
    resource.get({id: id}).then(function(response) {
      this.property = response.data;
    }.bind(this));
  },
  methods: {
    saveProperty: function() {
      var id = this.property.id;
      var resource = this.$resource('/api/settings/property{/id}');
      resource.save({id: id},this.property).then(function(response) {
        console.log('success');
        //console.log(response.data);
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      });
    }
  }
}
