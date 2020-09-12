@extends('layouts.calendar')

@section('head')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>

    <meta name="csrf-token" id="token" value="{{ csrf_token() }}"/>

@stop

@section('content')
@stop

@section('foot')

    <script>
        $(document).ready(function(){
            moment.locale('de');
            var tempDates = 0;
            var calendar = $('#calendar').fullCalendar({
                firstDay: 1,
                lang: 'de',
                defaultView: 'agendaWeek',
                eventOverlap: false,
                defaultTimedEventDuration: '00:{{ $default_duration }}:00',
                selectable: true,
//                selectConstraint: 'businessHours',   // allow user to add appointment on disabled slots...
                eventConstraint: 'businessHours',
                businessHours: [
                    @if($timeframes != '')
                        @foreach($timeframes as $t)
                            {
                                'start' : '{{ $t->startTime }}',
                                'end' : '{{ $t->endTime }}',
                                'dow' : [{{ $t->day_of_week }}]
                            },
                        @endforeach
                    @endif
                    // {'start': '19:00', 'end': '20:00', 'dow':[1]} ///???
                ],
                hiddenDays: [
                ],
                events: [
                        @foreach($dates as $d)
                        <?php
                        if ($d->patient && $d->patient->patientmeta->name) {
                            $title = '[P] ' . $d->patient->patientmeta->name;
                        } else {
                            $title = '[Z] ' . $d->dentist_contact->dentistmeta->name;
                            $color = 'yellow';
                        }
                        ?>
                    {
                        title: "{!!  $title !!}",
                        start: '{{ str_replace(' ','T', $d->date) }}',
                        end: '{{ str_replace(' ','T', $d->end) }}',
                        patient_id: '{{$d->patient ? $d->patient->id : $d->dentist_contact->id }}',

                        @if($d->patient && $d->patient->patientmeta->name)
                        color: '#0984e3',
                        @else
                        color: '#26A65B',
                        @endif

                    },
                        @endforeach
                        @foreach(json_decode($holidays) as $k => $v)
                        {
                            title: "{!! $k !!}",
                            allDay: true,
                            start: '{{ $v }}',
                            color: 'red'
                        },
                        @endforeach
                        @foreach($excludes as $e)
                    {
                        title: 'Heute Keine Termine bitte!',
                        start: '{{ $e }} 00:00',
                        end: '{{ $e }} 23:59',
                        color: 'red'
                    },
                    @endforeach
                ],
                eventClick: function (calEvent, jsEvent, view) {
                    // if (calEvent.patient_id == '{{ $id }}') {
                    //     calendar.fullCalendar('removeEvents', calEvent._id);
                    // }

                    // console.log('Event: ' + calEvent.title);
                    // console.log('ID: ' + calEvent.patient_id);
                    // console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                    // console.log('View: ' + view.name);

                    // // change the border color just for fun
                    // $(this).css('border-color', 'red');

                },
                select: function (start, end, jsEvent, view) {
                    if (start.isBefore(moment())) {
                        calendar.fullCalendar('unselect');
                        return false;
                    }
                    var day = start;
                    // console.log(day);
                    var title = '{!! $patient_name !!}';
                    if (tempDates > 0) { // Wenn schon ein Termin angelegt wurde
                        calendar.fullCalendar('unselect');
                        return;
                    }


                    var result = bootbox.confirm({
                        title: 'Phasen-Termin bestätigen',
                        message: '<p>Möchten Sie einen Termin (' + day.format('DD.MM.YYYY HH:mm') + ' Uhr) mit <strong>' + title + '</strong>  anlegen?</p>',
                        buttons: {
                            'cancel': {
                                label: 'Abbrechen',
                                className: 'btn-danger',
                            },
                            'confirm': {
                                label: 'Termin eintragen',
                                className: 'btn-primary',
                            }
                        },
                        callback: function (result) {

                            if (result === false) {
                                return;
                            }
                            tempDates++; // Termin wurde angelegt und gezählt, damit kein weiterer Termin angelegt werden kann
                            // console.log(day.add({{ $default_duration }}, 'm').format('LLLL'));
                            var s = moment($('.labdate').val(), 'DD.MM.YYYY HH:mm');
                            var eventstart = day;
                            var eventend = eventstart.clone().add('{{ $default_duration }}', 'm');

                            // console.log('start is ' + eventend.format('YYYY-MM-DD HH:mm'));
                            // return;
                            // console.log([, ]);
                            // return;

                            var newEvent = calendar.fullCalendar('renderEvent', {
                                    title: title,
                                    start: eventstart,
                                    end: eventend
                                },
                                true // make the event "stick"
                            );

                            calendar.fullCalendar('unselect');
                            // console.log(eventstart.format('YYYY-MM-DD HH:mm:ss') + ' | '+ eventend.toString());
                            // return;
                            $.ajax({
                                url: '/api/{{ $type }}/{{ $id }}/save-date',
                                method: 'POST',
                                data: {
                                    text: title,
                                    datum: eventstart.format('YYYY-MM-DD HH:mm'),
                                    end: eventend.format('YYYY-MM-DD HH:mm'),
                                    // phase: $('#phases').val()
                                },
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value'),
                                },
                            }).done(function (res) {
                                // console.log(res);
                                location.reload();
                            }).fail(function (res) {
                                // console.log('=>' + res.data);
                            });
                        }
                    });
                    // //var title = prompt('Termin mit:');
                    if (result === false) {
                        return false;
                    }
                }
            });
        });
    </script>
@stop