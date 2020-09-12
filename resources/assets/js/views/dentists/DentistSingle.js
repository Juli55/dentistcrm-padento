import DentistSingle from './dentistsingle.template.html';

export default {
  template: DentistSingle,
  data() {
    return {
      lab: [],
      labs: [],
      selectedLab: [],
    };
  },
  created: function() {
    this.refresh();
  },
  methods: {
    savePassword: function() {
      if($('#password').val() != $('#password-again').val()) {
        alert('Die Passwörter stimmen nicht überein.');
        return false;
      } else {
        var id = this.lab.user.id;
        var resource = this.$resource('/api/userpassword{/id}');
        this.lab.user.password = $('#password').val();
        resource.save({id: id}, this.lab.user).then(function (response) {
          alert('Ihr Passwort wurde erfolgreich geändert.');
          console.log('success');
          //console.log(response.data);
        }, function (response) {
          alert('Ihr Passwort konnte nichtgeändert werden.');
          console.log('##### error #####');
          //console.log(response.data);
        });
      };
    },
    saveEmail: function() {
      if($('#email').val() != $('#email-again').val()) {
        alert('Die Passwörter stimmen nicht überein.');
        return false;
      } else {
        var id = this.lab.user.id;
        var resource = this.$resource('/api/user{/id}');
        resource.save({id: id}, this.lab.user).then(function (response) {
          console.log('success');
          //console.log(response.data);
        }, function (response) {
          console.log('##### error #####');
          //console.log(response.data);
        });
      };
    },
    saveLab: function() {
      var id = this.$route.params.id;
      var lab = this.$resource('/api/dentist{/id}');
      lab.save({id: id}, this.lab).then(function (response) {
        console.log('success');
        //console.log(response.data);
      }, function (response) {
        console.log('##### error #####');
        //console.log(response.data);
      });
    },
    whoAmI: function() {
        this.$http.get('/api/whoami')
            .then(response => {
                this.whoami = response.data;
            });
    },
    referDent: function() {
      var id = this.$route.params.id;
      this.$http.post('/api/dentist/' + id + '/lab/' + this.selectedLab).then(function(response) {
        console.log('success – dentist referred');
        this.refresh();
      }, function(response) {
        console.log('### error ###');
        //console.log(response.data);
      })
    },
    refresh: function() {
      $.getJSON('/api/labs', function(data) {
        this.labs = data.data.data;
      }.bind(this));
      // this.whoami = this.whoAmI();
      var id = this.$route.params.id;
      $.getJSON('/api/dentist/' + id, function(data) {
        this.lab = data;
      }.bind(this));
    }
  }
};
