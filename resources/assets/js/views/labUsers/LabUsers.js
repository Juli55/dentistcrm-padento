import LabUsers from './labusers.template.html';

export default {
    template: LabUsers,
    data() {
        return {
            hasThreeLabUsers: false,
            hasTwoLabUsers: false,
            sitename: 'Laborbenutzer',
            labUsers: [],
            newLabUser: {
                name: null,
                email: null,
                password: null,
                status: 'Aktiv',
                allowLogin: 'allow'
            },
            pagination: {
                total: 0,
                per_page: 100,
                from: 1,
                to: 0,
                current_page: 1,
            },
            offset: 4, // left and right padding from the pagination <span>,just change it to see effects
            orderby: {
                name: 'created_at',
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

        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');

    },
    ready: function () {

        this.getLabUsersCount();
        this.fetchItems(this.pagination.current_page);

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
                this.newLabUser.name != null &&
                this.newLabUser.email != null &&
                this.newLabUser.password != null
            );
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
        },

        isOwner() {
            return this.whoami.id === this.whoami.lab[0].user_id;
        }
    },

    components: {},

    methods: {
        getLabUsersCount: function () {
            this.$http.get('/api/getLabUsersCount', {}).then(
                function (response) {
                    response.data >= 3 ? this.hasThreeLabUsers = true : this.hasThreeLabUsers = false;
                    response.data >= 2 ? this.hasTwoLabUsers = true : this.hasTwoLabUsers = false;
                }.bind(this),
                function (response) {
                }.bind(this)
            );
        },

        toggleActive(user) {
            if(this.hasTwoLabUsers && user.status === 'Deaktiviert') return;

            this.$http.post(`/api/lab-users/toggle-active`, {user_id: user.id})
                .then(response => {
                    this.getLabUsersCount();
                    this.fetchItems(this.pagination.current_page);
                });
        },

        perpageChange: function (per_page) {
            this.pagination.per_page = per_page;
            this.fetchItems(1, true);
        },
        saveNewLabUser: function () {
            if (this.whoami.lab.length !== 0)
                this.newLabUser.labid = this.whoami.lab[0].id;
            else
                this.newLabUser.labid = this.whoami.labs[0].id;

            var data = this.newLabUser;
            this.$http.post('/api/createLabUsers', data).then(function (response) {
                //this.$router.go({name: 'admin.contactSingle', params: {id: response.data.patient_id}});
                window.location.reload();
                //this.fetchItems(1, true, 'sortby');
            }, function (error) {
                // $('#debug').addClass('active').find('.debugged-content').html(error.data);
                Messenger().post({
                    message: 'Laborbenutzer wurde nicht erstellt',
                    type: 'error'
                });
            });
        },
        deleteLabUser: function (id) {
            var data = {id: id};
            var resource = this.$resource('/api/lab-users/delete');

            var deleteContactBox = bootbox.confirm({
                title: 'Kontakt löschen',
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
                        $('#labUser-' + data.id).fadeOut();
                        this.fetchItems(1, true);
                        this.getLabUsersCount();
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
            this.data = {
                page: page,
                pagination: this.pagination,
                orderby: this.orderby,
                filter: this.filter,
                searchfor: this.search
            };
            this.$http.post('/api/allLabUsers', this.data).then(function (response) {
                console.log(response.data);
                this.$set('labUsers', response.data.data.data);
                this.$set('pagination', response.data.pagination);
                document.getElementById('main-content').scrollTop = 0;
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
        whoAmI: function () {
            this.$http.get('/api/whoami', {}).then(
                function (response) {
                    this.whoami = response.data;
                }.bind(this),
                function (response) {
                }.bind(this)
            );
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
    }
};
