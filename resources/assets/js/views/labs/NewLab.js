import NewLab from './newlab.template.html';

export default {
  template: NewLab,
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
        special1 : '',
        special2 : '',
        special3 : '',
        special4 : '',
        special5 : '',
        text : '',
        contact_email : '',
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
      },
      account: {},
      sitename: 'Neues Labor hinzufügen',
    };
  },
  created() {
  },
  ready () {
    $('#title').html(this.sitename);
    $('title').html(this.sitename);
  },
  methods: {
    saveLab: function(event) {
      event.preventDefault();
      console.log(this.account);
      var user = this.$resource('/api/newlab');
      user.save({}, {account:this.account}).then(function(response) {
        console.log(response.data);
        this.$router.go({ name: 'admin.labSingle', params: { id: response.data.data.lab_id } })
        //console.log(response.data);
        //this.lab = response.data;
      }, function(response) {
        console.log(response.data);
        $('#debug').addClass('active').find('.debugged-content').html(response.data);
        //console.log(response.data);
      }.bind(this));
    },
    tmpSave: function (event) {
      event.preventDefault();
      console.log(this.account);
      // var user = this.$resource('/api/newlab');
      // user.save({}, {user:this.user, lab:this.lab, labmeta:this.labmeta}).then(function(response) {
      //   console.log('success – new user created');
      //   //console.log(response.data);
      //   //this.lab = response.data;
      // }, function(response) {
      //   console.log('##### error #####');
      //   //console.log(response.data);
      // }.bind(this));
    },
    // saveLab: function(event) {
    //   event.preventDefault();
    //   console.log(this.lab);
    //   var user = this.$resource('/register');
    //   user.save({}, this.user).then(function(response) {
    //     console.log('success – new user created');
    //     //this.lab = response.data;
    //   }, function(response) {
    //     console.log('##### error #####');
    //     //console.log(response.data);
    //   }.bind(this));
    //   var lab = this.$resource('/api/newlab');
    //   lab.save({}, this.lab).then(function(response) {
    //     console.log('success – new lab created');
    //     //this.lab = response.data;
    //   }, function(response) {
    //     console.log('##### error #####');
    //     //console.log(response.data);
    //   }.bind(this));
    //   var labmeta = this.$resource('/api/newlabmeta');
    //   lab.save({}, this.labmeta).then(function(response) {
    //     console.log('success – new lab created');
    //     //this.lab = response.data;
    //   }, function(response) {
    //     console.log('##### error #####');
    //     //console.log(response.data);
    //   }.bind(this));
    // },
    // savePassword: function() {
    //   if($('#password').val() != $('#password_confirmation').val()) {
    //     alert('Die Passwörter stimmen nicht überein.');
    //     return false;
    //   } else {
    //     var id = this.lab.user.id;
    //     var resource = this.$resource('/api/user{/id}');
    //     resource.save({id: id}, this.lab.user).then(function (response) {
    //       console.log('success');
    //       //console.log(response.data);
    //     }, function (response) {
    //       console.log('##### error #####');
    //       //console.log(response.data);
    //     });
    //   };
    // }
  }
}
