import NewDent from './newdent.template.html';

export default {
  template: NewDent,
  data() {
    return {
      lab: {
        user_id : '',
        name : ''
      },
      labmeta: {
        lab_id : '',
        hello : '',
        contact_person : '',
        text : '',
        tel : '',
        count : '0',
        street : '',
        city : '',
        zip : '',
        url: ''
      },
      user: {
        email: '',
        password: ''
      }
    };
  },
  created() {
  },
  methods: {
    tmpSave: function (event) {
      event.preventDefault();
      var user = this.$resource('/api/newdent');
      user.save({}, {user:this.user, lab:this.lab, labmeta:this.labmeta}).then(function(response) {
        console.log('success – new user created');
        //console.log(response.data);
        //this.lab = response.data;
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      }.bind(this));
      //location.reload();
    },
    saveLab: function(event) {
      event.preventDefault();
      console.log(this.lab);
      var user = this.$resource('/api/newdent');
      user.save({}, this.user).then(function(response) {
        console.log('success – new user created');
        //this.lab = response.data;
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      }.bind(this));
      var lab = this.$resource('/api/newlab');
      lab.save({}, this.lab).then(function(response) {
        console.log('success – new lab created');
        //this.lab = response.data;
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      }.bind(this));
      var labmeta = this.$resource('/api/newlabmeta');
      lab.save({}, this.labmeta).then(function(response) {
        console.log('success – new lab created');
        //this.lab = response.data;
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      }.bind(this));
    },
    savePassword: function() {
      if($('#password').val() != $('#password_confirmation').val()) {
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
    }
  }
}
