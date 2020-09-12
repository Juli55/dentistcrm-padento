import ContactSingle from './contactsingle.template.html';
import PatientReferModal from '../../components/user/NewPatientReferModal.js';
import DateModal from '../../components/user/DateModal.js';
import CalendarModal from '../../components/user/CalendarModal.js';
// import VueDateTimePicker from '../../../../../node_modules/vue-datetime-picker/src/vue-datetime-picker.js';

var stages = {
    1: "stage first",
    2: "stage second",
    3: "stage third",
    4: "stage fourth",
    5: "stage fifth",
    6: "stage sixth"
};

export default {
    template: ContactSingle,
    components: {
        PatientReferModal,
        DateModal,
        CalendarModal,
        // 'vue-datetime-picker': require("../../../../../node_modules/vue-datetime-picker/src/vue-datetime-picker.js")
    },
    data() {
        return {
            debug: [],
            contact: {},
            labdate: '',
            properties: [],
            note: [],
            sitename: "Kontakt bearbeiten",
            // whoami: [],
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
            scrolling: false,
            showresults: false,
            showMap: true,
            map: null,
            map_settings: {
                center: {lat: 51.165691, lng: 10.451526},
                zoom: 8
            },
            labs: [],
            circleSettings: []
        };
    },
    created: function () {
        this.csrf = document.querySelector('#token').getAttribute('value');
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
        this.getAllLabs();
        this.loadContact();
        console.log('contacts loaded');
        // this.whoAmI();
        this.getEmployeeDate();
       // this.getTimeline();
        // this.getNoteTimeline();
    },
    ready: function () {
        this.loadContact();
        console.log('contacts loaded');
        var that = this;
        $('.contact-data').mouseover(function () {
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

        initMap() {
            this.map = new google.maps.Map(document.getElementById('map'), this.map_settings);
        },

        getCircleSettings () {
            this.$http.get('/api/distribution/circlesettings', this.data).then(function (response) {
                this.$set('circleSettings', response.data);
            }, function (error) {
                // console.
            });
        },

        setMapCenter(location) {
            this.map.setCenter(location);
        },

        addMarker(location, lab) {
            var contentString = "<h4>" + lab.name + "</h4>" +
                "<p>Adresse: " + lab.labmeta.street + ' ' + lab.labmeta.city + ' ' + lab.labmeta.zip + ' ' +  lab.labmeta.country_code.toUpperCase() +"</p>" +
                "<p>Kontakt Person: " + lab.labmeta.contact_person + "</p>" +
                "<p>Kontakt Telefonnummer: " + lab.labmeta.tel + "</p>";

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            var marker = new google.maps.Marker({
                position: location,
                map: this.map
            });

            marker.addListener('click', function () {
                infowindow.open(this.map, marker);
            });
        },

        setMarkers() {
            this.showMap = true;

            this.initMap();

            // this.results.labs.forEach(function(lab, index) {

            //   // var location = new google.maps.LatLng(parseFloat(lab.lab.lon), parseFloat(lab.lab.lat));
            //   var location = {lat: parseFloat(lab.lab.lon), lng: parseFloat(lab.lab.lat)};

            //   console.log(location);

            //   this.addMarker(location, lab);
            // }.bind(this));

            this.labs.forEach(function (lab, index) {
                // console.log(lab);
                // var location = new google.maps.LatLng(parseFloat(lab.lab.lon), parseFloat(lab.lab.lat));
                var location = {lat: parseFloat(lab.lon), lng: parseFloat(lab.lat)};

                // console.log(location);

                this.addMarker(location, lab);
            }.bind(this));

        },

        setCurrentZipMarker(zip, country) {
            this.$http.get('/api/distribution/lookup/' + zip + '/' + country).then(function (response) {
                var location = {lat: response.data.longitude, lng: response.data.latitude};

                var marker = new google.maps.Marker({
                    position: location,
                    map: this.map,
                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                });

                this.setMapCenter(location);

                var start = this.circleSettings.radius_start * 1;
                var inc = this.circleSettings.radius_inc * 1;
                var max = this.circleSettings.radius_max * 1;

                for (var i = start; i <= max; i += inc) {
                    this.drawCircle(location, i)
                }

            })
            ;
        },

        drawCircle(location, radius) {

            function shadeColor2(color, percent) {
                var f = parseInt(color.slice(1), 16), t = percent < 0 ? 0 : 255, p = percent < 0 ? percent * -1 : percent, R = f >> 16, G = f >> 8 & 0x00FF, B = f & 0x0000FF;
                return "#" + (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B)).toString(16).slice(1);
            }

            new google.maps.Circle({
                strokeColor: shadeColor2('#FF0000', radius / 100),
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: shadeColor2('#FF0000', radius / 100),
                fillOpacity: 0.15,
                map: this.map,
                center: location,
                radius: radius * 1000   // in meters
            });
        },

        getLabs: function (zip, email, country) {
            this.getCircleSettings();

            var resource = this.$resource('/api/distribution{/zip}{/country}{/email}');
            resource.get({zip: zip, country: country, email: email}).then(function (response) {
                console.log(response.data);
                this.results = response.data;
                this.showresults = true;

                if (this.showMap == true) {
                    this.setCurrentZipMarker(zip, country);
                    this.setMarkers();
                }

            }.bind(this), function (response) {
                // console.log(response.data);
                $('#debug').addClass('active').find('.debugged-content').html(response.data);
            }.bind(this));
        },

        getTimeline() {
             let form = {
                contact_id: this.$route.params.id,
                lab_id: this.contact.lab_id
            };

            this.$http.post('/api/timeline/forContact', form)
                .then(({data}) => {
                    this.timeline = data;
                });
        },

        getNoteTimeline() {
            let form = {
                contact_id: this.$route.params.id
            };

            this.$http.post('/api/note-timeline/forContact', form)
                .then(({data}) => {
                    this.note_timeline = data;
                });
        },

        sendAttachmentReminder: function () {
            let form = {
                patient_id: this.contact.id,
            };

            this.$http.post('/api/contact/sendReminder', form).then(function(response) {
                Messenger().post({
                    message: 'Anfrage für Dokumente versendet',
                    type: 'success',
                });
            });
        },

        patientUsed: function() {
            let data = {
                user_id: this.whoami.id,
                patient_id: this.contact.id
            };

            this.$http.post('/api/contact/used', data).then(function(response) {
                // console.log(response.data);
                this.getTimeline();
                if (response.data.user_id == this.whoami.id) {
                    $('#contact-error').hide();
                    $('#contact').fadeIn();
                } else {
                    $('#contact-error').html('Kontakt wird seid ' + moment(response.data.created_at, "YYYY-MM-DD HH:mm:ss").format('HH:mm:ss') + ' Uhr  von <strong>' + response.data.user.name + '</strong> verwendet. Zuletzt um ' + moment(response.data.updated_at, "YYYY-MM-DD HH:mm:ss").format('HH:mm:ss') + ' Uhr.');
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

        makequeued: function(id, type = null) {
            var resource = this.$resource('/api/contact/moveback');

            resource.save({}, { id: id, type: type }).then(function(response) {
                console.log(response.data);
                Messenger().post({
                    message: 'Patient zurückgeleitet',
                    type: 'success',
                    // showCloseButton: true
                });
                this.$router.go({ name: 'admin.contacts', params: {} });
            }, function(response) {
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                Messenger().post({
                    message: 'Patient nicht zurückgeleitet',
                    type: 'success',
                    // showCloseButton: true
                });
            });

            /*var makequeudBox = bootbox.confirm({
                title: 'Kontakt an Padento zurückleiten',
                message: '<p>Wenn Sie diesen Kontakt an das Padento-Team zurückschicken, werden wir versuchen mit diesem Kontakt aufzunehmen.</p><br/><form><label>Add Note (Required):</label><textarea name="" id="note" cols="30" rows="10" class="form-control" v-model="note" required></textarea></form>',
                buttons: {
                    'cancel': {
                        label: 'Abbrechen',
                        className: 'btn-danger'
                    },
                    'confirm': {
                        label: 'Jetzt zurückleiten',
                        className: 'btn-default'
                    }
                },
                callback: function(result) {
                    if (result === false) {
                        return;
                    }
                    resource.save({}, { id: id, type: type }).then(function(response) {
                        console.log(response.data);
                        Messenger().post({
                            message: 'Patient zurückgeleitet',
                            type: 'success',
                            // showCloseButton: true
                        });
                        this.$router.go({ name: 'admin.contacts', params: {} });
                    }, function(response) {
                        // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                        Messenger().post({
                            message: 'Patient nicht zurückgeleitet',
                            type: 'success',
                            // showCloseButton: true
                        });
                    });
                }
            });*/
        },
        loadContact: function() {
            var id = this.$route.params.id;

            var resource = this.$resource('/api/contact{/id}');
            resource.get({ id: id }).then(function(response) {
                if (response.data.error == 'nicht deiner') {
                    this.$router.go({ name: 'home', params: {} });
                }
                this.contact = response.data;
                if (response.data.latest_date && response.data.latest_date.date) {
                    this.labdate = moment(response.data.latest_date.date, "YYYY-MM-DD HH:mm").format('DD.MM.YYYY HH:mm');
                }
                this.patientUsed();

                if (this.isAdmin || this.isUser) {
                    this.getLabs(this.contact.patientmeta.zip, this.contact.patientmeta.email, 'de');
                }
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
            var resource = this.$resource('/api/date/delete');
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
            this.$http.post('/api/employeedate/save', { date: this.userDate, patient: this.contact }).then(function(response) {
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
            var resource = this.$resource('/api/contact{/id}');
            resource.get({ id: id }).then(function(response) {
                // console.log(response.data);
                this.contact = response.data;
                this.getTimeline();
            }.bind(this));

            var custom = this.$resource('/api/properties/all');
            custom.get().then(function(response) {
                this.properties = response.data;
            }.bind(this));
        },
        saveContact: function(event) {
            console.log(this.contact.labdate);
            this.contact.labdate = this.labdate;
            var that = this;
            var id = this.$route.params.id;
            var key = $(event.target);
            console.log(this.contact.archived);
            if (key.data('todo') == 'togglearchived') {
                this.contact.archived = key.data('archived');
            }
            if (key.data('input') == 'labdate') {
                // key.hide();
            };
            console.log(this.contact.archived);
            if (key.data('todo') == 'deletelabdate') {
                // key.hide();
                this.contact.deletelabdate = 'yes';
            }
            var resource = this.$resource('/api/contact{/id}/update');
            // console.log(this.contact.labdate);
            resource.save({ id: id }, this.contact).then(function(response) {
                if (key.data('input') == 'labdate') {
                    this.loadContact();
                    //this.getTimeline();
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
                    this.contact.deletelabdate = '';
                    this.contact.labdate = '';
                    this.labdate = '';
                    // this.loadContact();
                    // setTimeout(function() {
                    //     key.fadeIn();
                    // }, 1000);

                }
                this.patientUsed();
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
            for (var key in this.contact.props) {
                var id = this.contact.props[key].pivot.id;
                var resource = this.$resource('/api/settings/single-property{/id}');
                resource.save({ id: id }, this.contact.props[key].pivot).then(function(response) {
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
            this.$http.post('/api/contact/' + id + '/note', { note: this.note }).then(function(response) {
                $('#note').val('');
                Messenger().post({
                    message: 'Notiz gespeichert',
                    type: 'success'
                        // showCloseButton: true
                });

                // this.getNoteTimeline();
                this.patientUsed();
            }, function(response) {
                // console.log(response.data);
            }.bind(this));
            this.update();
        },
        approveContact: function() {
            var tempThis = this;
            var approveContactConfirm = bootbox.confirm({
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
                    var resource = tempThis.$resource('/api/contact{/id}/update');
                    tempThis.contact.confirmed = '1';
                    tempThis.contact.confirmed_by = tempThis.whoami.id;
                    // tempThis.contact.confirmer.name = tempThis.whoami.name;
                    resource.save({ id: id }, tempThis.contact).then(function(response) {
                        // console.log(response.data);
                        Messenger().post({
                            message: 'Kontakt bestätigt',
                            type: 'success',
                            // showCloseButton: true
                        });
                        this.patientUsed();
                        //this.getTimeline();
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

            if (key == 6 && this.contact.queued == 1) {
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
                        Vue.http.get('/admin/contact/' + id + '/toggle-phase/' + key);
                        Messenger().post({
                            message: 'Phase geändert',
                            type: 'success'
                        });
                        that.contact.phase = key;
                        that.getTimeline()
                    }
                });
            } else {
                Vue.http.get('/admin/contact/' + id + '/toggle-phase/' + key);
                Messenger().post({
                    message: 'Phase geändert',
                    type: 'success'
                });
                this.contact.phase = key;
                this.getTimeline();
            }
            this.patientUsed();
        },
        unobtainable: function() {
            var id = this.$route.params.id;
            Vue.http.get('/api/contact/' + id + '/unobtainable').then(function(response) {
                // this.contact = response.data;
                // console.log(response.data);
                this.patientUsed();
            }, function(response) {
                // console.log(response.data);
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

            this.$http.get('/api/todosForContact/' + id)
                .then(response => {
                    this.contact.tasks = response.data;
                });
        },

        createTask() {
            this.taskForm.contact_id = this.$route.params.id;

            this.$http.post('/api/todo', this.taskForm)
                .then(response => {
                    this.taskForm.title = '';

                    this.fetch();
                    this.getTimeline();
                });
        },

        editTask(task) {
            this.editTaskForm = task;
        },

        updateTask() {
            this.$http.patch('/api/todo/' + this.editTaskForm.id, this.editTaskForm)
                .then(response => {
                    this.editTaskForm = false;

                    this.fetch();
                    this.getTimeline();
                });
        },

        deleteTask(task) {
            if(!confirm('Möchten Sie dieses ToDo wirklich löschen?')) {
                return false;
            }

            this.$http.delete('/api/todo/' + task.id)
                .then(response => {
                    this.fetch();
                    this.getTimeline();
                });
        },

        toggleComplete(task) {
            let form = {
                task_id: task.id,
            };

            this.$http.post('/api/todo/toggleComplete', form)
                .then(response => {
                    this.fetch();
                    this.getTimeline();
                });
        },

        sortTasks(task) {
            let tasks = this.contact.tasks;

            tasks.splice(task.newIndex, 0, tasks.splice(task.oldIndex, 1)[0]);

            let ids = _.map(tasks, 'id');

            let form = {
                contact_id: this.$route.params.id,
                ids: ids
            };

            this.$http.post(`/api/todo/sort`, form)
                .then(response => {
                    this.contact.tasks = response.data;
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
            if (this.contact && this.contact.tasks.length) {
                return _.orderBy(this.contact.tasks, 'order', 'asc');
            }

            return [];
        }
    }
};
