import CalendarModalView from './calendarmodaldentist.template.html';

export default {
    template: CalendarModalView,
    created: function() {
    },
    ready () {
         var id = this.$route.params.id;
        $( "#modal-body" ).load( "/dentistcalendar/" + id);
        $('#calendar').fullCalendar('today');
    }
}
