import SettingsUserEditView from './settingsuseredit.template.html';

export default {
  template: SettingsUserEditView,
  data () {
    return {
      user: [],
      labs: [],
      allocatedlabs: [],
      all_roles: [],
      selectedRole: []
    };
  },
  created () {
    this.fetchData();

    var id = this.$route.params.id;

    var labs = this.$resource('/api/labs');
    labs.get({id:id}).then(function(response) {
      this.labs = response.data;
    }.bind(this));

    this.$http.get('/api/roles/all')
        .then(response => {
          this.all_roles = response.data;
        });
  },
  methods: {
    saveAllocatedLab: function () {
      this.$http.get('/api/allocatelab/' + this.user.id + '/' + this.allocatedlabs, []);
      this.fetchData();
    },
    fetchData: function() {
      var id = this.$route.params.id;
      var resource = this.$resource('/api/user{/id}');
      resource.get({id:id}).then(function(response) {
        this.user = response.data;
        this.selectedRole = response.data.roles[0].id;
      }.bind(this));
    },
    savePassword: function() {
      if($('#password').val() != $('#password_confirmation').val()) {
        alert('Die Passwörter stimmen nicht überein.');
        return false;
      } else {
        var id = this.user.id;
        var resource = this.$resource('/api/userpassword{/id}');
        this.user.password = $('#password').val();
        resource.save({id: id}, this.user).then(function (response) {
          alert('Ihr Passwort wurde erfolgreich geändert.');
          Messenger().post({
            message: 'Passwort geändert',
            type: 'success'
          });
        }, function (response) {
          Messenger().post({
            message: 'Passwort wurde nicht geändert',
            type: 'error'
          });
        });
      };
    },
    saveUser: function () {
        var id = this.$route.params.id;
        var resource = this.$resource('/api/user{/id}');
        resource.save({id: id}, this.user).then(function (response) {
          Messenger().post({
            message: 'Nutzerdaten geändert',
            type: 'success'
          });
        }, function (response) {
          Messenger().post({
            message: 'Nutzerdaten nicht geändert',
            type: 'error'
          });
          console.log('##### error #####');
        });
      // };
    },
    saveRole: function() {
      var id = this.$route.params.id;
      this.$http.get('/api/user/' + id + '/role/' + this.selectedRole);
      Messenger().post({
        message: 'Rolle geändert',
        type: 'success'
      });
    }
  }
}
