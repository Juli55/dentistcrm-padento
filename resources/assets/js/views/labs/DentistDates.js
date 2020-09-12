import MyDatesView from './dentistdates.template.html';

export default {
    template: MyDatesView,
    data () {
        return {
            dates: [],
            show: 'heute',
            sitename: "Termine",
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
            offset: 4, // left and right padding from the pagination <span>,just change it to see effects
            fetchData: {
                oldDate: {},
                date: {},
                newOldDate: {},
                newNowDate: {}
            },
            items_per_page: [
                {value: 15},
                {value: 50},
                {value: 100},
                {value: 150},
                {value: 200}
            ],
        }
    },
    computed: {
        oldDatePagesNumber: function () {
            if (!this.oldDatePagination.to) {
                return [];
            }
            var from = this.oldDatePagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.oldDatePagination.last_page) {
                to = this.oldDatePagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        datePagesNumber: function () {
            if (!this.datePagination.to) {
                return [];
            }
            var from = this.datePagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.datePagination.last_page) {
                to = this.datePagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        newOldPagesNumber: function () {
            if (!this.newOldDatePagination.to) {
                return [];
            }
            var from = this.newOldDatePagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.newOldDatePagination.last_page) {
                to = this.newOldDatePagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        newNowPagesNumber: function () {
            if (!this.newNowDatePagination.to) {
                return [];
            }
            var from = this.newNowDatePagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.newNowDatePagination.last_page) {
                to = this.newNowDatePagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        isOldDatePageActived: function () {
            return this.oldDatePagination.current_page;
        },
        isDatePageActived: function () {
            return this.datePagination.current_page;
        },
        isNewOldDatePageActived: function () {
            return this.newOldDatePagination.current_page;
        },
        isNewNowDatePageActived: function () {
            return this.newNowDatePagination.current_page;
        }
    },
    created () {
        // this.whoAmI();
        if (this.isUser) {
            this.queuedFilter = '1';
        }

        this.fetch();

        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
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
        whoAmI: function () {
            this.$http.get('/api/whoami')
                .then(response => {
                    this.whoami = response.data;

                    if (this.isUser) {
                        this.queuedFilter = '1';
                    }
                });
        },
        showDates: function (what) {
            this.show = what;
        },
        changePage: function (page,model) {
            this[model + 'Pagination'].current_page = page;
            this.fetch();
        },
        numPerPageChange: function (per_page, model) {
            this[model + 'Pagination'].current_page = 1;
            this[model + 'Pagination'].per_page = per_page;
            this.fetch();
        },
        fetch: function () {
            this.fetchData = {
                oldDate: this.oldDatePagination,
                date: this.datePagination,
                newOldDate: this.newOldDatePagination,
                newNowDate: this.newNowDatePagination
            };
             this.$http.post(`/api/mydates/dentist`, this.fetchData).then(function (response) {
                this.dates = response.data;
                this.oldDatePagination = response.data.old_dates;
                this.datePagination = response.data.dentist_dates;
                this.newOldDatePagination = response.data.today_old;
                this.newNowDatePagination = response.data.today_old;
            });
        }
    }
}





































































