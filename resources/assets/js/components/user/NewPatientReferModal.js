import PatientReferModal from './newpatientrefer-modal.template.html';


export default {
  template: PatientReferModal,
  props: ['title', 'selected'],
  data () {
    return {
      labs: [],
      refertype: 'na'
    };
  },
  created () {
    $.getJSON('/api/labs', function(data) {
      this.labs = data.data.data;
    }.bind(this));
    $('#title').html( this.sitename);
    // console.log(this.contact.lab_id);
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
    },
    refer: function(selected) {
      var id = this.$route.params.id;
      this.$http.post('/api/contact/' + selected.id + '/refer/' + id + '/' + this.refertype).then(function(response) {
        // console.log(response.data);
        location.reload();
      }, function(response) {
        Messenger().post({
          message: 'Fehler beim weiterleiten',
          type: 'error'
        });
        console.log(response.data);
      });
      //console.log(selected);
    }
  }
};
