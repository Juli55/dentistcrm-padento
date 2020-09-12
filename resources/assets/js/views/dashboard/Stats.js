import stats from './stats.template.html';
import Chart from 'chart.js';

export default {
    template: stats,

    components: {},

    data() {
        return {
            sitename: 'Statistiken',
            labs: [],
            labNames: 'Alle',
            form: {
                start_date: null,
                end_date: null,
                lab_ids: [],
                queued: null,
                active_lab: null,
            },
            labels: [],
            current_range: [],
            current_range_chart: null,
            previous_range: [],
            all: [],
            total: [],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        };
    },

    created() {
        if (this.isAdmin) {
            this.fetch();
            this.getLabs();
        }
        else {
            if (this.whoami.lab.length)
                var lab = this.whoami.lab[0];
            else
                var lab = this.whoami.labs[0];

            if (lab.id != 'undefined') {
                this.labs.push(lab);
                this.form.lab_ids.push(lab.id);
                // this.fetch();
            }
        }

        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
    },

    ready() {
        this.initializeDaterange();
    },

    methods: {
        fetch() {
            this.$http.post('/api/stats/contacts', this.form)
                .then(({data}) => {
                    this.labels = data.labels;
                    this.current_range = data.current_range;
                    this.previous_range = data.previous_range;
                    this.all = data.all;

                    this.calculateTotal();
                    this.loadCharts();
                });
        },

        withQueuedService() {
            this.form.queued = 1;
        },

        withoutQueuedService() {
            this.form.queued = 0;
        },

        withActiveLab() {
            this.form.active_lab = true;
        },

        withInactiveLab() {
            this.form.active_lab = false;
        },

        clearFilters() {
            this.form.queued = null;
            this.form.active_lab = null;
        },

        loadCharts() {
            this.loadCurrentRangeChart();
            this.loadPreviousRangeChart();
        },

        loadCurrentRangeChart() {
            if (this.current_range_chart) {
                this.current_range_chart.destroy();
            }

            let data = {
                labels: this.labels,
                datasets: [{
                    label: 'Ausgewählte Zeitspanne',
                    data: this.current_range,
                    backgroundColor: this.backgroundColor,
                    borderColor: this.borderColor,
                    borderWidth: this.borderWidth
                }]
            };

            this.current_range_chart = new Chart('current_range_chart', {
                type: 'bar',
                data: data,
            });
        },

        loadPreviousRangeChart() {
            if (this.previous_range_chart) {
                this.previous_range_chart.destroy();
            }

            let data = {
                labels: this.labels,
                datasets: [{
                    label: 'Vergleichs-Zeitspanne',
                    data: this.previous_range,
                    backgroundColor: this.backgroundColor,
                    borderColor: this.borderColor,
                    borderWidth: this.borderWidth
                }]
            };

            this.previous_range_chart = new Chart('previous_range_chart', {
                type: 'bar',
                data: data,
            });
        },

        totalContacts(data) {
            return _.sum(data);
        },

        percentage(value, data) {
            let result = (value * 100) / this.totalContacts(data);

            return result ? result.toFixed(2) : 0;
        },

        getLabs() {
            this.$http.get('/api/labs', {all: true}).then(response => {
                this.labs = response.data.data.data;
            });
        },

        calculateTotal() {
            this.$set('total', []);

            this.total[0] = _.sum(_.map(this.all, data => {
                return _.sum(data);
            }));

            this.total[1] = _.sum(_.map(this.all, data => {
                return data[0];
            }));

            this.total[2] = _.sum(_.map(this.all, data => {
                return data[1];
            }));

            this.total[3] = _.sum(_.map(this.all, data => {
                return data[2];
            }));

            this.total[4] = _.sum(_.map(this.all, data => {
                return data[3];
            }));

            this.total[5] = _.sum(_.map(this.all, data => {
                return data[4];
            }));

            this.total[6] = _.sum(_.map(this.all, data => {
                return data[5];
            }));

            this.total[7] = _.sum(_.map(this.all, data => {
                return data[6];
            }));
        },

        initializeDaterange() {
            let start = moment().subtract(5, 'months').startOf('month');
            let end = moment();

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    cancelLabel: 'Klar'
                },
                ranges: {
                    'Diesen Monat': [moment().startOf('month'), moment().endOf('month')],
                    'Im vergangenen Monat': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Letzten 3 Monate': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Letzten 6 Monate': [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Dieses Jahr': [moment().startOf('year'), moment().endOf('year')],
                }
            });

            $('#daterange').on('apply.daterangepicker', (ev, picker) => {
                $('#daterange').val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));

                this.form.start_date = picker.startDate.format('DD-MM-YYYY');
                this.form.end_date = picker.endDate.format('DD-MM-YYYY');
            });

            $('#daterange').on('cancel.daterangepicker', (ev, picker) => {
                $('#daterange').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));

                this.form.start_date = null;
                this.form.end_date = null;
            });
        },

        getLabNames() {
            let labIds = this.form.lab_ids;

            if (labIds.length) {
                this.labNames = this.labs.filter(lab => labIds.indexOf(lab.id) >= 0).map(lab => lab.name);
            } else {
                this.labNames = 'Alle';
            }
        },

        date(value) {
            return moment.utc(value, 'DD-MM-YYYY').local().format('DD.MM.YYYY')
        }
    },

    watch: {
        'form': {
            handler(value) {
                this.getLabNames();
                this.fetch();
            },
            deep: true
        },
    }
};
