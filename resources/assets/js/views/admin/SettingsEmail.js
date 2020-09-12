import SettingsEmail from './settingsemail.template.html';

const MailNames = [
    {
        'key': 'LaborMail1',
        'value': 'Labor Mail'
    }
];

export default {
    props: ['mailid'],
    template: SettingsEmail,
    data () {
        return {
            settings: [],
            testmail: [],
            sitename: 'Emails'
        };
    },
    created: function () {
        // Vue.config.debug = true;
        var resource = this.$resource('/api/settings/email');
        resource.get().then(function (response) {
            this.settings = response.data;
            Vue.nextTick(function () {
                let self = this;
                $('.noteditable').summernote({
                    callbacks: {
                        onBlur: function (event) {
                            self.saveMail(event);
                            //$('#submit-summernote').click();
                        }
                    }
                });

            }.bind(this));
        }.bind(this), function (response) {
            $('#debug').addClass('active').find('.debugged-content').html(response.data);
        });
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
//    $.getJSON('/api/settings/email', function(data) {
//      this.settings = data;
//    }.bind(this));
    },
    ready() {
        // setTimeout(function() {
        //   tinymce.init({
        //     selector:'.editable',
        //     plugins: 'code'
        //   });
        // },35);
        new Clipboard('.clip');
    },
    methods: {
        saveMail: function (event) {
            event.preventDefault();
            for (var key in this.settings) {
                var id = this.settings[key].id;
                this.settings[key].body = $('#body' + id).val();
                this.settings[key].footer = $('#footer' + id).val();

                var resource = this.$resource('/api/settings/email{/id}');
                console.log(this.settings[key]);
                resource.save({id: id}, this.settings[key]).then(function (response) {
                    // console.log(response.data);
                    Messenger().post({
                        message: 'E-Mail gespeichert',
                        type: 'success',
                        // showCloseButton: true
                    });
                }, function (response) {
                    // console.log(response.data);
                    Messenger().post({
                        message: 'E-Mail nicht gespeichert',
                        type: 'error',
                        // showCloseButton: true
                    });
                });
            }
            ;
        },
        sendTestMail: function (id) {
            console.log(id);
            for (var key in this.settings) {
                if (this.settings[key].id == id) {
                    var email = this.settings[key];
                    var data = {
                        mail: email,
                        to: this.testmail
                    };
                    this.$http.post('/api/send/test-mail/' + id, data).then(function (response) {
                        console.log(response.data);
                    }, function (response) {
                        console.log(response.data);
                    });
                }
            }
            return false;
        }
    },
    filters: {
        translateMailName: function (name) {
            $.map(this.MailNames, function (key, value) {
                if (name == value) {

                }
            })
        }
    }
};
