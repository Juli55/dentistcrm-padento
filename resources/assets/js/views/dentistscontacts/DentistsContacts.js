import DentistsContacts from './dentistscontacts.template.html';


// import DentistsTable from '../../components/dentists/DentistsTable.js';


import store from '../../stores/DentistsStore';

const phasemap = {
    0: 'Alle',
    1: 'Fremd',
    2: 'Vertrauen',
    3: 'Beziehung',
    4: 'Testkunde',
    5: 'B-Kunde',
    6: 'A-Kunde',
    7: 'Kein Interesse'
};
const classmap = {
    1: '.first.active',
    2: '.second.active',
    3: '.third.active',
    4: '.fourth.active',
    5: '.fifth.active',
    6: '.sixth.active'
};
const statusmap = {
    1: 'Alle (Ohne Archiv)',
    5: 'Archiviert'
};


export default {
    template: DentistsContacts,

    data() {
        return {

            shared: store,
            newdentistcontact: {
                name: '',
                zip: '',
                email: '',
                phone: '',
                labid: '',
                salutation: ''
            },
            dentists: [],
            labs: [],
            labname: [],
            lab: [],
            stats: [],
            phase: [],
            reverse: -1,
            phasename: [],
            sitename: "Zahnärzte",
            unconfirmed: '',
            filterWhat: 1,
            filterOptions: {
                phase: {
                    options: [
                        {value: 'reset', text: 'Alle'},
                        {value: '1', text: 'Fremd'},
                        {value: '2', text: 'Vertrauen'},
                        {value: '3', text: 'Beziehung'},
                        {value: '4', text: 'Testkunde'},
                        {value: '5', text: 'B-Kunde'},
                        {value: '6', text: 'A-Kunde'},
                        {value: '7', text: 'Kein Interesse'},
                    ],
                },
                queued: {
                    options: [
                        {value: 'reset', text: 'Alle'},
                        {value: '0', text: 'Nein'},
                        {value: '1', text: 'Ja'}
                    ],
                },
                documents: {
                    options: [
                        {value: 'reset', text: 'Alle'},
                        {value: '0', text: 'Keine Dokumente'},
                        {value: '1', text: 'Mit Dokumenten'}
                    ],
                },
                status: {
                    options: [
                        {value: 'reset', text: 'Alle (Ohne Archiv)'},
                        {value: 'archived', text: 'Archiviert'},
                    ],
                },
                lab: {
                    options: [],
                },
            },
            filter: {
                status: {
                    selected: 'reset',
                },
                phase: {
                    selected: 'reset',
                },
                queued: {
                    selected: 'reset',
                },
                documents: {
                    selected: 'reset',
                },
                lab: {
                    selected: 'reset',
                }
            },
            sortFilter: 'created_at',
            queuedFilter: '',
            // whoami: [],
            pagination: {
                total: 0,
                per_page: 100,
                from: 1,
                to: 0,
                current_page: 1,
            },
            type: '',
            offset: 4, // left and right padding from the pagination <span>,just change it to see effects
            orderby: {
                name: 'dentist_contacts.created_at',
                sort: 'desc'
            },
            data: {},
            items_per_page: [
                {value: 25},
                {value: 50},
                {value: 100},
                {value: 150},
                {value: 200}
            ],
        };
    },

    created: function () {

        this.$http.get('/api/labs', {all: true}).then(response => {
            if(response.data.data) {
                this.labs = response.data.data.data;
                this.filterOptions.lab.options = this.labs;
            }
        });


        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');

        // this.setPhaseName('0');
    },


    ready: function () {

        if (store.dentists.stored == true) {
            this.pagination = store.dentists.data.pagination;
            this.orderby = store.dentists.data.orderby;
            this.contacts = store.dentists.contacts;
            this.stats = store.dentists.stats;
            this.filter = store.dentists.data.filter;
        } else {
            this.fetchItems(this.pagination.current_page);
        }

        $('#newdentistcontact').on('hidden.bs.modal', (e) => {
            this.newdentistcontact.salutation = '';
            this.newdentistcontact.name = '';
            this.newdentistcontact.zip = '';
            this.newdentistcontact.email = '';
            this.newdentistcontact.phone = '';

        });

        this.setLabName(this.labname);

        //initialize labs
        this.$http.get('/api/labs', {all: true}).then(response => {
            if (response.data.data) {
                this.labs = response.data.data.data;
                this.filterOptions.lab.options = this.labs;
            }
        });
    },


    watch: {
        'filter': {
            handler: function (value) {
                this.fetchItems(this.pagination.current_page, true, 'filter');
            },
            deep: true
        },
        search: function (value) {
            this.fetchItems(1, true, 'search');
        }
    },

    computed: {
        formReady: function () {
            return (
                this.newdentistcontact.name != null &&
                this.newdentistcontact.zip != null &&
                this.newdentistcontact.email != null &&
                this.newdentistcontact.phone != null
            );
        },


        unconfirmed: function () {
            if (this.stats.all != '') {
                return this.stats.all - this.stats.confirmed;
            } else {
                return '';
            }
        },


        isActived: function () {
            return this.pagination.current_page;
        },
        pagesNumber: function () {
            if (!this.pagination.to) {
                return [];
            }
            var from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },


    /*
    components: {
        "contacts-table": ContactsTable
    },
    */




    methods: {
        clearfilter: function () {
            location.reload();
        },

        dentistUsed: function () {
            var data = {user_id: this.whoami.id, dentist_contact_id: this.dentists.id};
        },


        perpageChange: function (per_page) {
            this.pagination.per_page = per_page;
            this.fetchItems(1, true);
        },

        savedentist: function () {
            if (!this.isAdmin) {
                if (this.whoami.lab.length) {
                    this.newdentistcontact.labid = this.whoami.lab[0].id;
                } else {
                    this.newdentistcontact.labid = this.whoami.labs[0].id;
                }
            }

            if (!this.newdentistcontact.labid) {
                Messenger().post({
                    message: 'Zahnarzt nicht angelegt',
                    type: 'error'
                });
                return;
            }

            var data = {contact: this.newdentistcontact};
            this.$http.post('/dankedentist', data).then(function (response) {
                this.$router.go({name: 'admin.dentistContactSingle', params: {id: response.data.dentist_id}});
            }, function (error) {
                Messenger().post({
                    message: 'Zahnarzt nicht angelegt',
                    type: 'error'
                });
            });
        },

        deletedentist: function (id) {
            var data = {id: id};
            var resource = this.$resource('/api/dentist/delete');

            var deleteDentistBox = bootbox.confirm({
                title: 'Zahnarzt löschen',
                message: '<p>Sicher?</strong>',
                buttons: {
                    'cancel': {
                        label: 'Abbrechen',
                        className: 'btn-danger'
                    },
                    'confirm': {
                        label: 'Löschen',
                        className: 'btn-default'
                    }
                },
                callback: function (result) {
                    if (result === false) {
                        return;
                    }
                    resource.save({}, data).then(function (response) {
                        $('#contact-' + data.id).fadeOut();
                        this.fetchItems(1, true);
                        Messenger().post({
                            message: 'Kontakt gelöscht',
                            type: 'success'
                        });
                    }, function (error) {
                        // $('#debug').addClass('active').find('.debugged-content').html(error.data);
                        Messenger().post({
                            message: 'Kontakt nicht gelöscht',
                            type: 'error'
                        });
                    });

                }
            });
        },

        labName(lab) {
            if (lab.status === 'inaktiv') {
                return lab.name + ' [inaktiv]';
            }

            return lab.name;
        },

        setLabName: function (lab) {
            if (lab == 0) {
                this.labname = 'Alle';
            } else {
                $.map(this.labs, function (value, key) {
                    if (lab == value.id) {
                        this.labname = value.name;
                    }
                }.bind(this));
            }
        },

        sortBy: function (name) {
            if (this.orderby.sort == 'asc') {
                var sort = 'desc';
            } else {
                var sort = 'asc';
            }
            this.orderby = {name: name, sort: sort};
            this.fetchItems(1, true, 'sortby');
        },


        fetchItems: function (page, killCache = false, initby = null) {
            // $('#spinner').addClass('show');
            this.data = {
                page: page,
                pagination: this.pagination,
                orderby: this.orderby,
                filter: this.filter,
                searchfor: this.search
            };
            this.$http.post('/api/alldentists', this.data).then(function (response) {
                console.log(response.data);
                this.$set('dentists', response.data.data.data);
                this.$set('pagination', response.data.pagination);
                this.$set('stats', response.data.stats);
                store.dentists = {
                    stored: true,
                    data: this.data,
                    dentists: this.dentists,
                    stats: this.stats
                };
                document.getElementById('main-content').scrollTop = 0;
                // $('#spinner').removeClass('show');
                if (page > 1) {
                    Messenger().post({
                        message: 'Seite ' + page + ' geladen',
                        type: 'success'
                    });
                }

            }.bind(this), function (error) {
                $('#debug').addClass('active').find('.debugged-content').html(error.data);
            }).bind(this);

            this.debug = this.data;
        },

        changePage: function (page) {
            this.pagination.current_page = page;
            this.fetchItems(page, true, 'changepage');
        },

        archiveContact: function (contactID) {
        },
        filterPhase: function (phase) {
            if (phase == '0') {
                this.phase = [];
            } else {
                this.phase = phase;
            }
            this.setPhaseName(phase);
        },
        whoAmI: function () {
            this.$http.get('/api/whoami', {}).then(
                function (response) {
                    this.whoami = response.data;
                }.bind(this),
                function (response) {
                }.bind(this)
            );
        },

        setPhaseName: function (phase) {
            var name = $.map(phasemap, function (value, key) {
                if (phase == key) {
                    this.phasename = value;
                }
            }.bind(this));
        },
        isPastDate: function (date) {
            return moment(date).isBefore(moment());
        }
    },

    events: {},

    filters: {
        niceDate: function (date) {
            if (date) {
                var nicedate = date.split(/[- :]/)[2].split(' ')[0] + '.' +
                    date.split(/[- :]/)[1] + '.' +
                    date.split(/[- :]/)[0] + ' – ' +
                    date.split(/[- :]/)[3] + ':' + date.split(/[- :]/)[4];
                return nicedate;
            }

            return '-';
        },
        'translate': function (phase) {
            let name = $.map(phasemap, function (value, key) {
                if (phase == key) {
                    return value;
                }
                ;
            });
            return name;
        },
        'shorten': function (ref) {

            var l = document.createElement("a");
            l.href = ref;

            return l.hostname;
        },
    }


};
