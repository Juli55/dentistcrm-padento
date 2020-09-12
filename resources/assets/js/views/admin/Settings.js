import Settings from './settings.template.html';

export default {
    template: Settings,
    data() {
        return {
            settings: [],
            sitename: 'Einstellungen'
        };
    },
    created() {
        var resource = this.$resource('/api/settings/all');
        resource.get({}, function (data) {
            this.settings = data;
             Vue.nextTick(function () {
                 $('textarea[id^="summernote"]').summernote({
                    callbacks: {
                        onBlur: function () {
                            $('#submit-summernote').click();
                        }
                    }
                });

            });

        }.bind(this));
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
    },

    ready() {

    },

    methods: {
        saveSettings: function (event) {
            event.preventDefault();
            for (var key in this.settings) {
                var id = this.settings[key].id;

                if (this.settings[key].name == 'Textbereich 1') {
                    this.settings[key].value = $('#summernote1').val();
                }
                if (this.settings[key].name == 'Textbereich 2') {
                    this.settings[key].value = $('#summernote2').val();
                }
                if (this.settings[key].name == 'Textbereich 1 AT') {
                    this.settings[key].value = $('#summernote3').val();
                }
                if (this.settings[key].name == 'Textbereich 2 AT') {
                    this.settings[key].value = $('#summernote4').val();
                }
                if (this.settings[key].name == 'No Labs Found Body') {
                    this.settings[key].value = $('#summernote5').val();
                }

                var resource = this.$resource('/api/settings/all{/id}');
                //console.log(this.settings[key]);
                resource.save({id: id}, this.settings[key]).then(function (response) {
                    Messenger().post({
                        message: 'Einstellungen gespeichert',
                        type: 'success'
                    });
                    //console.log(response.data);
                }, function (response) {
                    Messenger().post({
                        message: 'Einstellungen nicht gespeichert',
                        type: 'error'
                    });
                    //console.log(response.data);
                });
            }
            ;
        }
    }
};
