import dashboard from './dashboard.template.html';

export default {
    template: dashboard,
    components: {},
    data() {
        return {
            contacts: [],
            labs: [],
            feeds: [],
            properties: [],
            note: [],
            sitename: "Padento Übersicht",
            // whoami: [],
            dates: [],
            fetchData: {
                oldDate: {},
                date: {},
                newOldDate: {},
                newNowDate: {},
                dashboard: true
            },
            oldDatePagination: {
                total: 0,
                per_page: 15,
                from: 1,
                to: 0,
                current_page: 1,
            },
            datePagination: {
                total: 0,
                per_page: 15,
                from: 1,
                to: 0,
                current_page: 1,
            },
            newOldDatePagination: {
                total: 0,
                per_page: 15,
                from: 1,
                to: 0,
                current_page: 1,
            },
            newNowDatePagination: {
                total: 0,
                per_page: 15,
                from: 1,
                to: 0,
                current_page: 1,
            },
        };
    },
    created: function () {
        /*  $.getJSON('/api/mydates', function(data) {
            this.dates = data;
          }.bind(this));*/
        this.fetchData = {
            oldDate: this.oldDatePagination,
            date: this.datePagination,
            newOldDate: this.newOldDatePagination,
            newNowDate: this.newNowDatePagination,
            dashboard: true

        };
        this.$http.post(`/api/mydates?dentists=true`, this.fetchData).then(function (response) {
            this.dates = response.data;
            this.oldDatePagination = response.data.old_dates;
            this.datePagination = response.data.dates;
            this.newOldDatePagination = response.data.today_old;
            this.newNowDatePagination = response.data.today_old;
        });
        this.update();
        $('#title').html(this.sitename);

        var id = this.$route.params.id;

        // this.whoAmI();
    },
    ready: function () {
    },
    methods: {
        update: function () {

            $.getJSON('/api/feeds', function (data) {
                this.feeds = data;
            }.bind(this));
            $.getJSON('/api/latestcontacts', function (data) {
                this.contacts = data;
            }.bind(this));
        },
        whoAmI: function () {
            this.$http.get('/api/whoami')
                .then(response => {
                    this.whoami = response.data;
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
    }
};
