import LabSettingsView from './labsettings.template.html';
import _ from 'lodash';

export default {
    template: LabSettingsView,

    data() {
        return {
            labs: [],
            sitename: 'Einstellungen',
            startTime: '',
            endTime: '',
            timeFrames: [],
            selected: [],
            newTimeFrame: '',
            timeFrame: '',
            excludedDays: [],
            excludedDayBlocks: [],
            weekdays: [],
            show: []
        }
    },

    created() {
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');

        this.updateSettings();

        this.getWeekdays();
        setTimeout(this.initCalendar, 500);
    },

    ready() {
        $('.trigger').mouseover(function() {
            $('.datetimepicker').datetimepicker({
                locale: 'de',
                disabledTimeIntervals: false,
                format: 'DD.MM.YYYY',
                // format: 'Y-m-d H:i'
            });
        });

    },

    watch: {
        selectDay: 'addNewTimeFrame',
        excludeDay: 'addExcludedDay'
    },

    methods: {
        initCalendar: function() {
            $('.datetimepicker').datetimepicker({
                locale: 'de',
                disabledTimeIntervals: false,
                format: 'DD.MM.YYYY',
                // format: 'Y-m-d H:i'
            });
        },

        getWeekdays: function() {
            $.getJSON('/api/weekdays', function(data) {
                this.weekdays = data;
            }.bind(this));
        },

        addNewTimeFrame: function(day) {
            this.timeFrames.push({
                'day': day,
                'id': '',
                'startTime': '08:00:00',
                'endTime': '16:00:00'
            });
        },

        // Save new timeframes for given lab
        saveTimeFrames: function(lab) {
            this.timeFrames.forEach(function(timeFrame) {
                this.saveTimeFrame(timeFrame, lab);
            }.bind(this));
        },

        saveTimeFrame: function(timeFrame, lab) {
            var resource = this.$resource('/api/lab/' + lab.id + '/timeframes');
            var tempFrames = [];
            resource.save({}, { 'timeFrame': timeFrame }).then(function(response) {
                console.log(response.data);
                // this.updateSettings();

                Messenger().post({
                    message: 'Zeitraum gespeichert',
                    type: 'success',
                    // showCloseButton: true
                });

                lab.timeframes.push(response.data);
                this.timeFrames.$remove(timeFrame);

                //console.log(response.data);
            }, function(response) {
                console.log(response.data);
                $('#debug').addClass('active').find('.debugged-content').html(response.data);
                Messenger().post({
                    message: 'Fehler beim Speichern eines Zeitraums',
                    type: 'error',
                    // showCloseButton: true
                });
                // console.log('### error ###');
                //console.log(response.data);
            });
        },

        saveSetting: function(setting) {
            var resource = this.$resource('/api/settings/lab/' + setting.id);
            resource.save(setting).then(function(response) {
                Messenger().post({
                    message: 'Einstellung gespeichert',
                    type: 'success',
                    // showCloseButton: true
                });
                //console.log(response.data);
            }, function(response) {
                Messenger().post({
                    message: 'Fehler beim Speichern von Einstellungen',
                    type: 'error',
                    // showCloseButton: true
                });
                //console.log(response.data);
            });
        },

        getTimeframes: function() {
            var resource = this.$resource('/api/lab/' + this.labs[0].id + '/timeframes');
            resource.get().then(function(response) {
                console.log('success');
                //console.log(response.data);
            }, function(response) {
                console.log('### error ###');
                //console.log(response.data);
            });
        },

        removeTimeframe: function(timeframe, lab) {
            this.$http.post('/api/timeframe/' + timeframe.id + '/remove').then(function(response) {
                Messenger().post({
                    message: 'Zeitraum entfernt',
                    type: 'success',
                    // showCloseButton: true
                });
                lab.timeframes.$remove(timeframe);
                //console.log(response.data);
            }, function(response) {
                console.log('### error ###');
                //console.log(response.data);
            });
            // location.reload();
        },

        removeNewTimeframe: function(timeframe) {
            this.timeFrames.$remove(timeframe);
        },

        addExcludedDay: function(day) {
            this.excludedDays.push({
                'day': day
            });
            this.saveExcludedDay(day);
        },

        removeSetting: function(day, lab) {
            var resource = this.$http.delete('/api/settings/lab/' + day.id).then(function(res) {
                Messenger().post({
                    message: 'Einstellung entfernt',
                    type: 'success',
                    // showCloseButton: true
                });

                lab.settings.$remove(day);
                //console.log(res.data);
            }, function(res) {
                //console.log(res.data);
                console.log('### error ###');
            });
            // location.reload();
        },

        removeExcludedDay: function(day) {
            this.excludedDays.$remove(day);
        },

        saveExcludedDay: function(day) {
            var resource = this.$http.post('/api/settings/lab/exclude-day/' + day).then(function(res) {
                Messenger().post({
                    message: 'Tag ausgeschlossen',
                    type: 'success',
                    // showCloseButton: true
                });
                //console.log(res.data);
            }, function(response) {
                console.log('### error ###');
                //console.log(response.data);
            });
        },

        saveDate: function(date, till = null) {
            if (!date) {
                return;
            }
            var data = [date, till];
            var resource = this.$http.post('/api/settings/lab/exclude-day/' + data).then(function(res) {
                console.log(res.data);
                // return;
                Messenger().post({
                    message: 'Datum gespeichert',
                    type: 'success',
                    // showCloseButton: true
                });
                //console.log(res.data);
            }, function(response) {
                console.log(response.data);
                // $('#debug').addClass('active').find('.debugged-content').html(response.data);
                // return;
                //console.log(response.data);
            });
            location.reload();
        },

        updateSettings: function() {
            $.getJSON('/api/settings/lab', function(data) {
                this.labs = data;

                this.prepareBlocks();
            }.bind(this));
        },

        prepareBlocks() {
            let settings = _.filter(this.labs[0].settings, {name: 'excluded_day'});

            for(let i=0; i<settings.length; i++) {
                let from = moment(settings[i].value);
                let to = from;

                for(let j=i+1; j<settings.length; j++) {
                    let next = moment(settings[j].value);

                    if (next.diff(to, 'days') === 1) {
                        to = next;
                        i = j;
                    }
                }

                this.excludedDayBlocks.push({
                    from: from,
                    to: from.diff(to) ? to : null,
                });
            }

        },

        removeBlock(block) {
            let settings = _.filter(this.labs[0].settings, {name: 'excluded_day'});

            let from = block.from, to = block.to;

            if (to === null) {
                to = from.clone();
            }

            while(to.diff(from, 'days') >= 0) {
                _.forEach(settings, (setting) => {
                    let date = moment(setting.value);

                    if(date.diff(from, 'days') === 0) {
                        this.removeSetting(setting, this.labs[0]);
                        this.excludedDayBlocks.$remove(block);
                    }
                });

                from.add(1, 'days');
            }
        }
    },

    filters: {
        weekDayNames: function(data) {
            var name = $.map(this.weekdays, function(day) {
                if (data == day.id) {
                    return day.name;
                } else {};
            }.bind(this));
            return name;
        }
    }
}
