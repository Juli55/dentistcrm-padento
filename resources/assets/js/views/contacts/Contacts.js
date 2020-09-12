import AllContacts from './contacts.template.html';
import ContactsTable from '../../components/contacts/ContactsTable.js';
import store from '../../stores/ContactsStore.js';

const phasemap = {
    0: 'Alle',
    1: 'Neu',
    2: 'Kontaktaufnahme',
    3: 'Termin',
    4: 'In Betreuung',
    5: 'Auftrag erhalten',
    6: 'Kein Interesse'
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
    2: 'Bestätigt',
    3: 'Unbestätigt',
    4: 'Ohne Labor',
    5: 'Archiviert'
};

export default {
    template: AllContacts,
    data() {
        return {
            shared: store,
            newcontact: {
                name: '',
                zip: '',
                email: '',
                phone: '',
                labid: '',
            },
            contacts: [],
            stats: [],
            labs: [],
            labname: [],
            lab: [],
            phase: [],
            reverse: -1,
            phasename: [],
            sitename: 'Kontakte',
            unconfirmed: '',
            filterWhat: 1,
            filterOptions: {
                phase: {
                    options: [
                        {value: 'reset', text: 'Alle'},
                        {value: '1', text: 'Neu'},
                        {value: '2', text: 'Kontaktaufnahme'},
                        {value: '3', text: 'Besuchstermin vereinbart'},
                        {value: '4', text: 'In Betreuung'},
                        {value: '5', text: 'Auftrag erhalten'},
                        {value: '6', text: 'Kein Interesse'},
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
                lab: {
                    options: [],
                },
                status: {
                    options: [
                        {value: 'reset', text: 'Alle (Ohne Archiv)'},
                        {value: 'confirmed', text: 'Bestätigt'},
                        {value: 'unconfirmed', text: 'Unbestätigt'},
                        {value: 'archived', text: 'Archiviert'},
                        {value: 'requested_deletation', text: 'Beantragte Löschung'},
                    ],
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
                name: 'patients.created_at',
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
            deleteContactForm: {
                id: null,
                send_mail: false,
                delete_all: false,
            }
        };
    },
    created: function () {

        this.$http.get('/api/labs', {all: true}).then(response => {
            if (response.data.data) {
                this.labs = response.data.data.data;
                this.filterOptions.lab.options = this.labs;
            }
        });

        /*$.getJSON('/api/labs', function (data) {
            this.labs = data.data.data;
            this.filterOptions.lab.options = this.labs;
        }.bind(this));*/
        //test

        // this.whoAmI();

        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');

        this.setPhaseName('0');
        this.setLabName(this.labname);
    },
    ready: function () {
        if (store.contacts.stored == true) {
            this.pagination = store.contacts.data.pagination;
            this.orderby = store.contacts.data.orderby;
            this.contacts = store.contacts.contacts;
            this.stats = store.contacts.stats;
            this.filter = store.contacts.data.filter;
            // this.searchfor = store.contacts.data.searchfor;
            // this.search    = store.contacts.data.searchfor;
            // $('#search').val(this.search);
        } else {
            this.fetchItems(this.pagination.current_page);
        }

        $('#newcontact').on('hidden.bs.modal', (e) => {
            this.newcontact.salutation = '';
            this.newcontact.name = '';
            this.newcontact.zip = '';
            this.newcontact.email = '';
            this.newcontact.phone = '';
        });

        $('#deletecontact').on('hidden.bs.modal', e => {
            this.deleteContactForm.id = null;
            this.deleteContactForm.send_mail = false;
            this.deleteContactForm.delete_all = false;
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
                this.newcontact.name != null &&
                this.newcontact.zip != null &&
                this.newcontact.email != null &&
                this.newcontact.phone != null &&
                this.newcontact.labid != null
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

    components: {
        "contacts-table": ContactsTable
    },

    methods: {
        labName(lab) {
            if (lab.status === 'inaktiv') {
                return lab.name + ' [inaktiv]';
            }

            return lab.name;
        },

        clearfilter: function () {
            location.reload();
        },
        patientUsed: function () {
            var data = {user_id: this.whoami.id, patient_id: this.contact.id};
        },
        perpageChange: function (per_page) {
            this.pagination.per_page = per_page;
            this.fetchItems(1, true);
        },
        savecontact: function () {
            if (this.isCrmUser) {
                this.newcontact.labid = this.whoami.lab[0].id;
            }

            var data = {contact: this.newcontact};
            this.$http.post('/danke', data).then(function (response) {
                this.$router.go({name: 'admin.contactSingle', params: {id: response.data.patient_id}});
            }, function (error) {
                // $('#debug').addClass('active').find('.debugged-content').html(error.data);
                Messenger().post({
                    message: 'Kontakt nicht angelegt',
                    type: 'error'
                });
            });
        },

        confirmDeleteContact: function (contact) {
            this.deleteContactForm.id = contact.id;

            if(contact.requested_deletation_at) {
                this.deleteContactForm.send_mail = true;
            }

            $('#deletecontact').modal('show');
        },

        deletecontact: function () {
            let resource = this.$resource('/api/contact/delete');

            resource.save({}, this.deleteContactForm)
                .then(response => {
                    $('#contact-' + this.deleteContactForm.id).fadeOut();

                    this.fetchItems(1, true);

                    $('#deletecontact').modal('hide');

                    Messenger().post({
                        message: 'Kontakt gelöscht',
                        type: 'success'
                    });
                }, function (error) {
                    $('#deletecontact').modal('hide');

                    // $('#debug').addClass('active').find('.debugged-content').html(error.data);
                    Messenger().post({
                        message: 'Kontakt nicht gelöscht',
                        type: 'error'
                    });
                });
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
            this.$http.post('/api/allcontacts', this.data).then(function (response) {
                console.log(response.data);
                this.$set('contacts', response.data.data.data);
                this.$set('pagination', response.data.pagination);
                this.$set('stats', response.data.stats);
                store.contacts = {
                    stored: true,
                    data: this.data,
                    contacts: this.contacts,
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
        isPastDate(date) {
            return moment(date).isBefore(moment(), 'day');
        },
        setPhaseName: function (phase) {
            var name = $.map(phasemap, function (value, key) {
                if (phase == key) {
                    this.phasename = value;
                }
            }.bind(this));
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
