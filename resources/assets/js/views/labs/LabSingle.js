import LabSingle from './labsingle.template.html';

export default {

  template: LabSingle,
  data() {
    return {
      lab_id: null,
      lab: [],
      hasCrmAccess: false,
      hasDentistCrmAccess: false,
      //hasExtraUsers: false,
      status: [],
      // whoami: [],
      images: {
          logo: null,
          kontaktfoto: null,
      },
      kontaktfoto: [],
      country_codes: [
          {
              'key': 'de',
              'name': 'Deutschland'
          },
          {
              'key': 'at',
              'name': 'Österreich'
          },
      ],
      groups: [
        {
          'key': '0',
          'name': 'Standard Labor'
        },


        {
          'key': 5,
          'name': 'Zahnarzt Labor'
        },


        {
          'key': 1,
          'name': 'Kontaktverwaltung'
        },


          /*
        {
          'key': 2,
          'name': 'Doppeltes Budget'
        },
        {
          'key': 4,
          'name': 'Kontaktverwaltung & doppeltes Budget'
        }

        */


      ],
      stati: [
        {
          'key': 'aktiv',
          'name': 'Aktiv'
        },
        {
          'key': 'inaktiv',
          'name': 'Inaktiv'
        },
      ],
      allowlogin: [
        {
          'key': 'allow',
          'name': 'Erlauben',
        },
        {
          'key': 'disallow',
          'name': 'Nicht erlauben',
        },
      ],
      sitename: '',
    };
  },
  created: function() {
    $(function() {
      Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
    });

    this.lab_id = this.$route.params.id;

    this.getLab();
    this.checkCrmAccess();
    this.checkDentistCrmAccess();
    //this.checkHasExtraUsers();
    this.getStats();
    // this.whoAmI();
    this.getLabImages();
    // setTimeout(function() { $('#kontaktfoto').fileinput(); }, 500);
    // setTimeout(function() { $('#logo').fileinput(); }, 500);

    let token = document.querySelector('#token').getAttribute('value');
    setTimeout(function() { $('#csrf-token').val(token); }, 500);
    setTimeout(function() { $('#csrf-token2').val(token); }, 500);
  },
  watch: {
    'lab.user.allowlogin' : function() {
        this.saveLab();
    },
    'lab.status' : function() {
        this.saveLab();
    },
  },
  methods: {

    getLab() {
        this.$http.get('/api/lab/' + this.lab_id, {}).then(
            function(response) {
                this.lab = response.data;
                console.log(response.data);
            }.bind(this),
            function(response) {
                console.log('fail');
                console.log(response.data);
            }.bind(this)
        );
    },

    checkCrmAccess() {
        this.$http.get(`/api/labs/${this.lab_id}/check-crm-access`)
            .then(response => {
                this.hasCrmAccess = response.data.authorized;
            });
    },

    toggleCrmAccess() {
        this.$http.post(`/api/labs/${this.lab_id}/toggle-crm-access`)
            .then(response => {
                this.hasCrmAccess = response.data.authorized;
            })
    },

    checkDentistCrmAccess() {
         this.$http.get(`/api/labs/${this.lab_id}/check-dentist-crm-access`)
            .then(response => {
                this.hasDentistCrmAccess = response.data.authorized;
            });
    },

    toggleDentistCrmAccess() {
        this.$http.post(`/api/labs/${this.lab_id}/toggle-dentist-crm-access`)
            .then(response => {
                this.hasDentistCrmAccess = response.data.authorized;
            })
    },

    /*checkHasExtraUsers() {
          this.$http.get(`/api/labs/${this.lab_id}/check-has-extra-users`)
              .then(response => {
                  this.hasExtraUsers = response.data.authorized;
              });
      },

    toggleHasExtraUsers() {
          this.$http.post(`/api/labs/${this.lab_id}/toggle-has-extra-users`)
              .then(response => {
                  this.hasExtraUsers = response.data.authorized;
              })
      },*/

    getLabImages() {
        this.$http.get(`/api/labs/${this.lab_id}/images`)
            .then(response => {
                if (response.data.logo) {
                    this.images.logo = response.data.logo[0];
                }
                if (response.data.kontaktfoto) {
                    this.images.kontaktfoto = response.data.kontaktfoto[0];
                }
            });
    },

    savePassword: function() {
      if($('#password').val() != $('#password_confirmation').val()) {
        alert('Die Passwörter stimmen nicht überein.');
        return false;
      } else {
        var id = this.lab.user.id;
        var resource = this.$resource('/api/userpassword{/id}');
        this.lab.user.password = $('#password').val();
        resource.save({id: id}, this.lab.user).then(function (response) {
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
    uploadFile: function() {
    },
    saveEmail: function() {
      if($('#email').val() != $('#email-again').val()) {
        alert('Die E-Mail-Adressen stimmen nicht überein.');
        return false;
      } else {
        var id = this.lab.user.id;
        var resource = this.$resource('/api/user{/id}');
        resource.save({id: id}, this.lab.user).then(function (response) {
          console.log('success');
        }, function (response) {
          console.log('##### error #####');
        });
      };
    },
    getStats: function() {
      $.getJSON('/api/lab/' + this.lab_id + '/stats', function(data) {
        this.status = data;
      }.bind(this));
    },
    saveLab: function() {
      console.log(this.lab);

      var lab = this.$resource('/api/lab{/id}');
      lab.save({id: this.lab_id}, this.lab).then(function (response) {
        console.log(response.data);
        Messenger().post({
          message: 'Profil aktualisiert',
          type: 'success'
        });
      }, function (response) {
        // console.log(response.data);
        // $('html').html(response.data);
        Messenger().post({
          message: 'Profil nicht aktualisiert',
          type: 'error'
        });
      });
    },
    whoAmI: function() {
        this.$http.get('/api/whoami')
            .then(response => {
                this.whoami = response.data;
            });
    },
    removeLab: function(id) {
      var http = this.$http;
      var removeLabBox = bootbox.confirm({
        title: 'Labor löschen?',
        message: '<p>Sicher? Damit werden ebenfalls alle Kontakte, die dem Labor zugeordnet sind, gelöscht.</strong><p><small>Im Notfall könnten wir (Pinetco) diese Daten retten.</small>',
        buttons: {
          'cancel': {
            label: 'Abbrechen',
            className: 'btn-danger'
          },
          'confirm': {
            label: 'Ja, Labor jetzt löschen',
            className: 'btn-default'
          }
        },
        callback: function(result) {
          if (result === false) {
            return;
          }
          http.get('/api/lab/' + id + '/delete').then(function(response) {
            console.log(response.data);
            Messenger().post({
              message: 'Lab removed successfully.',
              type: 'success'
            });
            this.$router.go({ name: 'admin.labs', params: {} });
          }, function(response) {
            Messenger().post({
              message: 'Lab removed successfully.',
              type: 'success'
            });
          });
        }
      });
    }
  },
  filters: {
    'count': function(collection) {
      return collection.length;
    }
  }
};
