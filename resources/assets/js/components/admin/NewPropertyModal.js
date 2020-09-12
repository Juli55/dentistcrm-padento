import NewPropertyModal from '../../components/admin/newproperty-modal.template.html';


export default {
  template: NewPropertyModal,
  props: ['title'],
  data () {
    return {
      property: {
        'type' : 'text',
        'name' : '',
        'default' : ''
      },
      options: [
        { 'text': 'Text', 'value' : 'text' },
        { 'text': 'Zahl', 'value' : 'zahl' }
      ]
    };
  },
  created () {
  },
  methods: {
    saveProperty: function() {
      var resource = this.$resource('/api/settings/property/new');
      resource.save({},this.property).then(function(response) {
        console.log(response.data);
      }, function(response) {
        console.log(response.data);
      });
      $('#newproperty').css({'opacity' : 0});
      location.reload();
    }
  }
};
