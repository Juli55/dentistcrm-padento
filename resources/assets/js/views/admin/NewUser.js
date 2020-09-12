import NewUser from './newuser.template.html';

export default {
  template: NewUser,
  data () {
    return {
      user: {
        name: '',
        email: '',
        password_confirmation: ''
      },
    };
  },
  created () {
  },
  methods: {
    saveUser: function(event) {
      event.preventDefault();
      //console.log(this.user);
      var user = this.$resource('/register');
      user.save({}, this.user).then(function(response) {
        console.log('success – new user created');
        //console.log(response.data);
        history.back();
        //this.lab = response.data;
      }, function(response) {
        console.log('##### error #####');
        //console.log(response.data);
      }.bind(this));

    }
  }
}
