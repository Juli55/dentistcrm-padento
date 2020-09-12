import DateModalView from './datemodal.template.html';


export default {
    template: DateModalView,
    props: ['title'],
    data() {
        return {
            labs: [],
            selected: [],
            contact: []
        };
    },
    created() {
        var id = this.$route.params.id;
        var resource = this.$resource('/api/contact{/id}');
        resource.get({ id: id }).then(function(response) {
            this.contact = response.data;
        }.bind(this));
    },
    ready() {
        var specificDates = ['24/04/2016', '17/04/2016'];
        var hoursToTakeAway = [
            [14, 15],
            [17]
        ];
        $('#datepicker').datetimepicker({
            locale: 'de',
            format: 'd.m.Y H:i'
        });

    },
    methods: {
        saveProperty: function() {
            var resource = this.$resource('/api/settings/property/new');
            resource.save({}, this.property).then(function(response) {
                console.log(response.data);
            }, function(response) {
                console.log(response.data);
            });
            $('#newproperty').css({ 'opacity': 0 });
        },
        saveDate: function(selected) {
            var id = this.$route.params.id;
            this.$http.post('/api/contact/' + id + '/save-date/', { 'date': this.selected }).then(function(response) {
                console.log('success');
                console.log(response.data);
                location.reload();
            }, function(response) {
                console.log(response.data);
            });
            console.log(selected);
        }
    }
};
