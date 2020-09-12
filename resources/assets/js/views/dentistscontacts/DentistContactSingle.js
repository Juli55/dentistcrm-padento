import DentistContactSingle from './dentistcontactsingle.template.html';

// import PatientReferModal from '../../components/user/NewPatientReferModal.js';
import DateModal from '../../components/user/DateModal.js';
import CalendarModalDentist from '../../components/user/CalendarModalDentist.js';

var stages = {
    1: "stage first",
    2: "stage second",
    3: "stage third",
    4: "stage fourth",
    5: "stage fifth",
    6: "stage sixth"
};


export default {
    template: DentistContactSingle,
    components: {
        //PatientReferModal,
        DateModal,
        CalendarModalDentist,
    },
    data() {
        return {
            debug: [],
            dentist: {},
            labdate: '',
            properties: [],
            note: [],
            sitename: "Zahnarzt bearbeiten",
            dates: [],
            timeFrames: [],
            userDate: [],
            csrf: null,
            timeline: false,
            note_timeline: false,
            taskForm: {
                title: '',
            },
            editTaskForm: false,
            showresults: false,
            labs: [],

            //todo dragging
            scrolling: false
        };
    },

    created: function () {
        this.csrf = document.querySelector('#token').getAttribute('value');
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
        this.getAllLabs();
        this.loadDentist();
        this.getEmployeeDate();
        this.getTimeline();
    },

    ready: function () {
        var that = this;
        $('.dentistcontact-data').mouseover(function () {
            that.initDatepicker();
        });
    },

    filters: {
        niceDate: function (date) {
            var nicedate = moment(date).format('DD.MM.YYYY HH:mm');
            // var nicedate = date.split(/[- :]/)[2].split(' ')[0] + '.' +
            //     date.split(/[- :]/)[1] + '.' +
            //     date.split(/[- :]/)[0] + ' – ' +
            //     date.split(/[- :]/)[3] + ':' + date.split(/[- :]/)[4];
            return nicedate + ' Uhr';
        },
    },

    methods: {
        getAllLabs () {
            this.$http.get('/api/labs', this.data).then(function (response) {
                if (response.data.data) {
                    this.$set('labs', response.data.data.data);
                }
            }, function (error) {
                // console.
            });
        },

        getTimeline() {
            let form = {
                contact_id: this.$route.params.id
            };

            this.$http.post('/api/timeline/dentist/forContact', form)
                .then(({data}) => {
                    this.timeline = data;
                });
        },

        getNoteTimeline() {
            let form = {
                contact_id: this.$route.params.id
            };

            this.$http.post('/api/note-timeline/dentist/forContact', form)
                .then(({data}) => {
                    this.note_timeline = data;
                });
        },

        sendAttachmentReminder: function () {
            let form = {
                dentist_contact_id: this.dentist.id,
            };

            this.$http.post('/api/dentist/sendReminder', form).then(function(response) {
                Messenger().post({
                    message: 'Anfrage für Dokumente versendet',
                    type: 'success',
                });
            });
        },

        dentistUsed: function() {
            let data = {
                user_id: this.whoami.id,
                dentist_contact_id: this.dentist.id
            };
            this.$http.post('/api/dentist/used', data).then(function(response) {
                // console.log(response.data);
                if (response.data.user_id == this.whoami.id) {
                    $('#dentistcontact-error').hide();
                    $('#dentistcontact').fadeIn();
                } else {
                    $('#dentistcontact-error').html('Kontakt wird seid ' + moment(response.data.created_at, "YYYY-MM-DD HH:mm:ss").format('HH:mm:ss') + ' Uhr  von <strong>' + response.data.user.name + '</strong> verwendet. Zuletzt um ' + moment(response.data.updated_at, "YYYY-MM-DD HH:mm:ss").format('HH:mm:ss') + ' Uhr.');
                }
            }, function(response) {
                console.log(response.data);
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                Messenger().post({
                    message: 'Fehler beim überprüfen, ob Kontakt in Verwendung ist.',
                    type: 'error',
                    showCloseButton: true,
                });
            });
        },


        confirmMakequeued() {
            $('#confirmMakequeued').modal('show');
        },





        /*
        makequeued: function(id, type = null) {
            var resource = this.$resource('/api/dentist/moveback');

            resource.save({}, {id: id, type: type}).then(function (response) {
                console.log(response.data);
                Messenger().post({
                    message: 'Patient zurückgeleitet',
                    type: 'success',
                    // showCloseButton: true
                });
                this.$router.go({name: 'admin.contacts', params: {}});
            }, function (response) {
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                Messenger().post({
                    message: 'Patient nicht zurückgeleitet',
                    type: 'success',
                    // showCloseButton: true
                });
            });
        },

        */

        loadDentist: function() {
            var id = this.$route.params.id;
            var resource = this.$resource('/api/dentist{/id}');
            resource.get({ id: id }).then(function(response) {
                if (response.data.error == 'nicht deiner') {
                    this.$router.go({ name: 'home', params: {} });
                }
                this.dentist = response.data;
                if (response.data.latest_date && response.data.latest_date.date) {
                    this.labdate = moment(response.data.latest_date.date, "YYYY-MM-DD HH:mm").format('DD.MM.YYYY HH:mm');
                }
                this.dentistUsed();

            }.bind(this), function(response) {
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                Messenger().post({
                    message: 'Fehler beim Laden des Kontaktes',
                    type: 'error',
                    showCloseButton: false
                });
            }.bind(this));
        },

        initDatepicker: function() {

        },

        getEmployeeDate: function() {
            var getemployeedate = this.$resource('/api/employeedate/get{/id}');
            getemployeedate.get({ id: this.$route.params.id }).then(function(response) {
                this.userDate = response.data;
            }.bind(this));
        },
        deleteEmployeeDate: function() {
            this.userDate = '';
            this.saveEmployeeDate();
        },


        deleteDate: function(event) {
            var key = $(event.target);
            var id = key.data('id');
            var resource = this.$resource('/api/date/delete/dentist');
            var makequeudBox = bootbox.confirm({
                title: 'Termin löschen?',
                message: 'Sind Sie sich sicher, dass Sie den Termin löschen wollen?',
                buttons: {
                    'cancel': {
                        label: 'Abbrechen',
                        className: 'btn-danger'
                    },
                    'confirm': {
                        label: 'Jetzt löschen',
                        className: 'btn-default'
                    }
                },
                callback: function(result) {
                    if (result === false) {
                        return;
                    }
                    resource.save({}, { id: id }).then(function(response) {
                        console.log(response.data);
                        // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                        key.parent().parent().fadeOut();
                        Messenger().post({
                            message: 'Termin gelöscht',
                            type: 'success',
                            // showCloseButton: true
                        });
                    }, function(response) {
                        // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                        Messenger().post({
                            message: 'Termin nicht gelöscht zurückgeleitet',
                            type: 'success',
                            // showCloseButton: true
                        });
                    });
                }
            });
        },
        saveEmployeeDate: function() {
            this.$http.post('/api/employeedate/dentist/save', { date: this.userDate, patient: this.dentist }).then(function(response) {
                console.log(response.data);
                Messenger().post({
                    message: 'Mitarbeitertermin aktualisiert',
                    type: 'success'
                });
            }, function(response) {
                console.log(response.data);
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
            }.bind(this));
        },
        update: function() {
            var id = this.$route.params.id;
            var resource = this.$resource('/api/dentist{/id}');
            resource.get({ id: id }).then(function(response) {
                // console.log(response.data);
                this.dentist = response.data;
                this.getTimeline();
            }.bind(this));

            var custom = this.$resource('/api/properties/all');
            custom.get().then(function(response) {
                this.properties = response.data;
            }.bind(this));
        },

        saveDentist: function(event) {
            console.log(this.dentist.labdate);
            this.dentist.labdate = this.labdate;
            var that = this;
            var id = this.$route.params.id;
            var key = $(event.target);
            console.log(this.dentist.archived);
            if (key.data('todo') == 'togglearchived') {
                this.dentist.archived = key.data('archived');
            }
            if (key.data('input') == 'labdate') {
                // key.hide();
            };
            console.log(this.dentist.archived);
            if (key.data('todo') == 'deletelabdate') {
                // key.hide();
                this.dentist.deletelabdate = 'yes';
            }
            var resource = this.$resource('/api/dentist{/id}/update');
            // console.log(this.dentist.labdate);
            resource.save({ id: id }, this.dentist).then(function(response) {
                if (key.data('input') == 'labdate') {
                    this.loadDentist();
                    this.getTimeline();
                    setTimeout(function() {
                        key.fadeIn();
                    }, 1000);
                }
                // console.log(response.data)
                Messenger().post({
                    message: 'Kontakt gespeichert',
                    type: 'success'
                    // showCloseButton: true
                });
                if (key.data('todo') == 'deletelabdate') {
                    this.dentist.deletelabdate = '';
                    this.dentist.labdate = '';
                    this.labdate = '';
                    this.loadDentist();
                    // setTimeout(function() {
                    //     key.fadeIn();
                    // }, 1000);

                }
                this.dentistUsed();
            }, function(response) {
                // console.log(response.data);
                this.debug = response.data;
                Messenger().post({
                    message: 'Fehler beim Speichern',
                    type: 'error',
                    // showCloseButton: true
                });
                if (key.data('input') == 'tel' || key.data('input') == 'mobile') {
                    Messenger().post({
                        message: 'Keine gültige Telefonnummer',
                        type: 'error',
                        // showCloseButton: true
                    });
                }
            });

        },


        saveProps: function() {
            for (var key in this.dentist.props) {
                var id = this.dentist.props[key].pivot.id;
                var resource = this.$resource('/api/settings/single-property{/id}');
                resource.save({ id: id }, this.dentist.props[key].pivot).then(function(response) {
                    Messenger().post({
                        message: 'Kontakt gespeichert',
                        type: 'success'
                        // showCloseButton: true
                    });
                }, function(response) {
                    // console.log(response.data);
                });
            };
        },
        saveNote: function(note) {
            var id = this.$route.params.id;
            this.note = $('#note').val();
            // console.log(this.note);
            this.$http.post('/api/dentist/' + id + '/note', { note: this.note }).then(function(response) {
                $('#note').val('');
                Messenger().post({
                    message: 'Notiz gespeichert',
                    type: 'success'
                    // showCloseButton: true
                });

               //  this.getNoteTimeline();
                this.dentistUsed();
            }, function(response) {
                // console.log(response.data);
            }.bind(this));
            this.update();
        },

        approveDentist: function() {
            var tempThis = this;
            var approveDentistConfirm = bootbox.confirm({
                title: 'Kontakt bestätigen',
                message: 'Hiermit bestätigen Sie den Kontakt. Seine E-Mail-Adresse wird dadurch sichtbar. Stellen Sie sicher, dass Sie sich die Einverständnis vom Kontakt geholt haben.',
                buttons: {
                    'cancel': {
                        label: 'Abbrechen',
                        className: 'btn-danger'
                    },
                    'confirm': {
                        label: 'Kontakt bestätigen',
                        className: 'btn-default'
                    }
                },
                callback: function(result) {
                    if (result === false) {
                        return;
                    }
                    var id = tempThis.$route.params.id;
                    var resource = tempThis.$resource('/api/dentist{/id}/update');
                    tempThis.dentist.confirmed = '1';
                    tempThis.dentist.confirmed_by = tempThis.whoami.id;
                    resource.save({ id: id }, tempThis.dentist).then(function(response) {
                        // console.log(response.data);
                        Messenger().post({
                            message: 'Kontakt bestätigt',
                            type: 'success',
                            // showCloseButton: true
                        });
                        this.dentistUsed();
                        this.getTimeline();
                    }, function(response) {
                        // console.log(response.data);
                        Messenger().post({
                            message: 'Kontakt nicht bestätigt',
                            type: 'error',
                            // showCloseButton: true
                        });
                    });
                }
            });
            // location.reload();
        },


        activatePhase: function(event) {


            var id = this.$route.params.id;
            var that = this;
            var key = $(event.target).data('phase');

            if (key == 6 && this.dentist.queued == 1) {
                var confirmlastphase = bootbox.confirm({
                    title: 'Kontakt hat kein Interesse',
                    message: 'Wenn Sie auf bestätigen klicken, leiten Sie den Kontakt an das Labor weiter.',
                    buttons: {
                        'cancel': {
                            label: 'Abbrechen',
                            className: 'btn-danger'
                        },
                        'confirm': {
                            label: 'Bestätigen',
                            className: 'btn-default'
                        }
                    },
                    callback: function(result) {
                        if (result === false) {
                            return;
                        }
                        Vue.http.get('/admin/dentist/' + id + '/toggle-phase/' + key);
                        Messenger().post({
                            message: 'Phase geändert',
                            type: 'success'
                        });
                        that.contact.phase = key;
                        that.getTimeline()
                    }
                });
            } else {
                Vue.http.get('/admin/dentist/' + id + '/toggle-phase/' + key);
                Messenger().post({
                    message: 'Phase geändert',
                    type: 'success'
                });
                this.dentist.phase = key;
                this.getTimeline();
            }
            this.dentistUsed();
        },
        unobtainable: function() {
            var id = this.$route.params.id;
            Vue.http.get('/api/dentist/' + id + '/unobtainable').then(function(response) {
                this.dentistUsed();
            }, function(response) {
            });
        },
        whoAmI: function() {
            this.$http.get('/api/whoami')
                .then(response => {
                    this.whoami = response.data;
                });
        },
        calendarClick: function() {
            setTimeout(function() {
                $('.fc-today-button').click();
            }, 500);
        },

        fetch() {
            var id = this.$route.params.id;

            this.$http.get('/api/todosdentistForContact/' + id)
                .then(response => {

                    this.dentist.tasks = response.data;
                });
        },

        createTask() {
            this.taskForm.contact_id = this.$route.params.id;

            this.$http.post('/api/tododentist', this.taskForm)
                .then(response => {
                    this.taskForm.title = '';

                    this.fetch();
                });
        },

        editTask(task) {
            this.editTaskForm = task;
        },

        updateTask() {
            this.$http.patch('/api/tododentist/' + this.editTaskForm.id, this.editTaskForm)
                .then(response => {
                    this.editTaskForm = false;

                    this.fetch();
                });
        },

        deleteTask(task) {
            if(!confirm('Willst du es wirklich löschen?')) {
                return false;
            }

            this.$http.delete('/api/tododentist/' + task.id)
                .then(response => {
                    this.fetch();
                });
        },

        toggleComplete(task) {
            let form = {
                task_id: task.id,
            };

            this.$http.post('/api/tododentist/toggleComplete', form)
                .then(response => {
                    this.fetch();
                });
        },

        sortTasks(task) {
            let tasks = this.dentist.tasks;

            tasks.splice(task.newIndex, 0, tasks.splice(task.oldIndex, 1)[0]);

            let ids = _.map(tasks, 'id');

            let form = {
                contact_id: this.$route.params.id,
                ids: ids
            };

            this.$http.post(`/api/tododentist/sort`, form)
                .then(response => {
                    this.dentist.tasks = response.data;
                });
        },

        handleTaskMove (/**Event*/evt, /**Event*/originalEvent) {
            if(evt.relatedRect.top < 100) {
              let self = this;
              if(!this.scrolling) {
                var container = $(".content");
                $(".content").animate({ scrollTop:  $(".content").scrollTop()-200}, 1000, function () {
                  self.scrolling = true
                }).promise().done(function () {
                  self.scrolling = false
                });
              }
            }
      	},

        filters: {
            linebreaks: function(data) {
                var text = [];
                var lines = text.split(/\n/);
                lines.each(function(line) {
                    text += '<p>' + line + '</p>';
                });
                return text;
            }
        }

    },

    computed: {
        sortedTasks() {
            if (this.dentist && this.dentist.tasks.length) {
                return _.orderBy(this.dentist.tasks, 'order', 'asc');
            }

            return [];
        }
    }
};
