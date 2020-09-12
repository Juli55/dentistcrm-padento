import CalendarModalView from './calendarmodal.template.html';

export default {
    template: CalendarModalView,
    created: function () {
    },
    ready() {
        var id = this.$route.params.id;
        $("#modal-body").load("/calendar/" + id);
        $('#calendar').fullCalendar('today');
    }
}
