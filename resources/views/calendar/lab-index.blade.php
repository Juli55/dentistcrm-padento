@extends('layouts.calendar')

@section('head')

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
  <meta name="csrf-token" id="token" value="{{ csrf_token() }}" />

@stop

@section('content')
@stop

@section('foot')

  <div id="calendar"></div>
    <script>
        $(document).ready(function(){
            var calendar = $('#calendar').fullCalendar({

                eventClick: function(calEvent, jsEvent, view) {

                    alert('Event: ' + calEvent.title);
                    alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                    alert('View: ' + view.name);

                    // change the border color just for fun
                    $(this).css('border-color', 'red');

                },
                firstDay: 1,
                lang: 'de',
                defaultView: 'agendaWeek',
                eventOverlap: false,
                defaultTimedEventDuration: '00:{{ $default_duration }}:00',
                selectable: false,

                select: function(day) {
                    var title = prompt('Termin mit:');
                    if (title) {
                        calendar.fullCalendar('renderEvent', {
                            title: title,
                            start: day,
                        }, true); // make the event "stick"

                        var date = day;
                        var text = title;
                        calendar.fullCalendar('unselect');
                        $.ajax({
                            url : '/api/contact/save-date',
                            method: 'POST',
                            data: {
                                text: this.title,
                                date: this.day
                            },
                            headers: {
                                'X-CSRF-TOKEN' : document.querySelector('#token').getAttribute('value')
                            },
                            error: function(res) {
                                console.log(res.responseText);
                            },
                        }).done(function(res) {
                            console.log(res);
                        });
                    }
                },
                selectConstraint: 'businessHours',
                eventConstraint: 'businessHours',
                businessHours: [
                    @if($timeframes != '')
                        @foreach($timeframes as $t)
                            {
                                'start' : '{{ $t->start }}',
                                'end'   : '{{ $t->stop }}',
                                'dow'   : [{{ $t->day_of_week }}]
                            },
                        @endforeach
                    @endif
                ],
                hiddenDays: [
                    @foreach($excludes as $e)
                        @if($e == '7')
                            0,
                        @else
                            {{ $e }},
                        @endif
                    @endforeach
                ],
                events: [
                    @foreach($dates as $d)
                        {
                            title: '{{ $d->patient->patientmeta->name }}',
                            start: '{{ $d->date }}',
                        },
                    @endforeach
                    @foreach(json_decode($holidays) as $k => $v)
                        {
                            title: "{!! $v->title !!}",
                            allDay: true,
                            start: '{{ $v->date }}',
                            color: 'red'
                        },
                    @endforeach
                ],
            });
        });
    </script>
@stop
