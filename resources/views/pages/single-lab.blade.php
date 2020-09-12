@extends('layouts.home')

@section('head')

@stop

@section('content')
    <div id="page" class="lab-page">
        <header class="lab-header">
            <div class="row">
                <div class="small-12 large-10 large-centered columns">
                    <h1>{{ $lab->name }}</h1>
                    <div class="row">
                        <div class="colum">
                            <div class="lab-box">
                                <div class="container-fluid">
                                    <div class="lab-box-header">
                                        <h23 id="cta"><b>Kommen Sie jetzt ins Handeln und machen Sie einen kostenfreien Beratungstermin. Rufen Sie jetzt an:</b></h23>
                                    </div>
                                    <p class="lab-box-content">Telefon: <a href="tel:{{ $lab->labmeta->tel }}">{{ $lab->labmeta->tel }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column medium-4">
                            <div class="lab-box contact-person">
                                <div class="lab-box-header">
                                    <strong>Ihr Ansprechpartner</strong>
                                </div>
                                <div class="lab-box-content">
                                    <p class="centered">
                                    @if($kontaktImage)
                                        <img class="lab-logo" src="{{ $kontaktImage->url }}" alt="{{ $lab->name }}"/>
                                    @elseif(file_exists(public_path().$pic))
                                        <img class="avatar" src="{{ url($pic) }}" alt="">
                                    @endif
                                    </p>
                                    <h4>{{ $lab->labmeta->contact_person }}</h4>
                                    <ul>
                                        <li>E-Mail: <a href="mailto:{{ $lab->user->email }}">{{ $lab->user->email }}</a>
                                        <li>Telefon: <a href="tel:{{ $lab->labmeta->tel }}">{{ $lab->labmeta->tel }}</a>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="medium-8 columns">
                            <div class="lab-box logo-slogan">
                                <div class="lab-box-content">
                                    <p class="centered">
                                    @if($logoImage)
                                        <img class="lab-logo" src="{{ $logoImage->url }}" alt="{{ $lab->name }}"/>
                                    @elseif(file_exists(public_path().$logo))
                                        <img class="lab-logo" src="{{ url($logo) }}" alt="{{ $lab->name }}"/>
                                    @else
                                        <img src="{{ url('img/informieren_made_in_ger.svg') }}" alt="{{ $lab->name }}"/>
                                    @endif
                                    </p>
                                    <p>{!! $lab->labmeta->hello !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @if (session('patientConfirmed'))
            <!-- section class="besttime">
                <div class="row">
                    <div class="medium-12 large-10 large-centered columns">
                        <h2 class="text-center">Wann können wir Sie am besten erreichen?</h2>
                        {{ Form::open(array('url' => '/wann', 'method' => 'POST')) }}
                        {{ Form::hidden('patient_id',session('patientConfirmed')->id) }}
                        {{ Form::hidden('lab_id',$lab->id) }}

                        <div class="row">
                            <div class="medium-4 columns">
                                {{ Form::label('from','Erreichbar werktags von:') }}
                                {{ Form::select('from', array(
                                    '' => '-- Auswählen --',
                                    '08:00Uhr' => '08:00Uhr',
                                    '09:00Uhr' => '09:00Uhr',
                                    '10:00Uhr' => '10:00Uhr',
                                    '11:00Uhr' => '11:00Uhr',
                                    '12:00Uhr' => '12:00Uhr',
                                    '13:00Uhr' => '13:00Uhr',
                                    '14:00Uhr' => '14:00Uhr',
                                    '15:00Uhr' => '15:00Uhr',
                                    '16:00Uhr' => '16:00Uhr',
                                    '17:00Uhr' => '17:00Uhr',
                                    '18:00Uhr' => '18:00Uhr',
                                    '19:00Uhr' => '19:00Uhr',
                                ), null, ['required']) }}
                            </div>
                            <div class="medium-4 columns">
                                {{ Form::label('till','bis:') }}
                                {{ Form::select('till', array(
                                    '' => '-- Auswählen --',
                                    '09:00Uhr' => '09:00Uhr',
                                    '10:00Uhr' => '10:00Uhr',
                                    '11:00Uhr' => '11:00Uhr',
                                    '12:00Uhr' => '12:00Uhr',
                                    '13:00Uhr' => '13:00Uhr',
                                    '14:00Uhr' => '14:00Uhr',
                                    '15:00Uhr' => '15:00Uhr',
                                    '16:00Uhr' => '16:00Uhr',
                                    '17:00Uhr' => '17:00Uhr',
                                    '18:00Uhr' => '18:00Uhr',
                                    '19:00Uhr' => '19:00Uhr',
                                    '20:00Uhr' => '20:00Uhr',
                                ), null, ['required']) }}
                            </div>
                            <div class="medium-4 columns">
                                {{ Form::submit('Abschicken',array('class' => 'button primary', 'style' => 'margin-top:1.4em')) }}
                            </div>
                        </div>

                        <div id="weekendTimes" style="display: none">
                            <div class="row">
                                <div class="medium-4 columns">
                                    {{ Form::label('weekend_from','von:') }}
                                    {{ Form::select('weekend_from', array(
                                        '' => '-- Auswählen --',
                                        '08:00Uhr' => '08:00Uhr',
                                        '09:00Uhr' => '09:00Uhr',
                                        '10:00Uhr' => '10:00Uhr',
                                        '11:00Uhr' => '11:00Uhr',
                                        '12:00Uhr' => '12:00Uhr',
                                        '13:00Uhr' => '13:00Uhr',
                                        '14:00Uhr' => '14:00Uhr',
                                        '15:00Uhr' => '15:00Uhr',
                                        '16:00Uhr' => '16:00Uhr',
                                        '17:00Uhr' => '17:00Uhr',
                                        '18:00Uhr' => '18:00Uhr',
                                        '19:00Uhr' => '19:00Uhr',
                                    ), null, []) }}
                                </div>
                                <div class="medium-4 columns">
                                    {{ Form::label('weekend_till','bis:') }}
                                    {{ Form::select('weekend_till', array(
                                        '' => '-- Auswählen --',
                                        '09:00Uhr' => '09:00Uhr',
                                        '10:00Uhr' => '10:00Uhr',
                                        '11:00Uhr' => '11:00Uhr',
                                        '12:00Uhr' => '12:00Uhr',
                                        '13:00Uhr' => '13:00Uhr',
                                        '14:00Uhr' => '14:00Uhr',
                                        '15:00Uhr' => '15:00Uhr',
                                        '16:00Uhr' => '16:00Uhr',
                                        '17:00Uhr' => '17:00Uhr',
                                        '18:00Uhr' => '18:00Uhr',
                                        '19:00Uhr' => '19:00Uhr',
                                        '20:00Uhr' => '20:00Uhr',
                                    ), null, []) }}
                                </div>
                                <div class="medium-4 columns"></div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </section -->
        @endif

        <section class="lab-about">
            <div class="row">
                <div class="small-12 large-10 large-centered columns">
                    <h3>Über uns und unsere Arbeit</h3>
                    <p>{!! str_replace("\n","<p>",$lab->labmeta->text) !!}</p>

                    <div class="info-box">
                        <div class="info-box-header">
                            <strong>Bilder:</strong>
                        </div>
                        <div class="info-box-content">
                            <div class="row small-up-2 medium-up-3">
                                @foreach($bilderImages as $image)
                                    <div class="column">
                                        <img src="{{ $image->url }}" alt="">
                                    </div>
                                @endforeach
                                @if(file_exists(getcwd().'/img/laborbilder/bild1'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild1{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                                @if(file_exists(getcwd().'/img/laborbilder/bild2'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild2{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                                @if(file_exists(getcwd().'/img/laborbilder/bild3'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild3{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                                @if(file_exists(getcwd().'/img/laborbilder/bild4'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild4{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                                @if(file_exists(getcwd().'/img/laborbilder/bild5'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild5{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                                @if(file_exists(getcwd().'/img/laborbilder/bild6'.$lab->id.'_neu.jpg'))
                                    <div class="column">
                                        <img src="{{ URL::to('/') }}/img/laborbilder/bild6{{$lab->id}}_neu.jpg" alt="">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($lab->labmeta->special1 != '')
                        <h3>Leistungen</h3>
                        <ul>
                            @if($lab->labmeta->special1 != '')
                                <li>{{$lab->labmeta->special1}}</li>
                            @endif
                            @if($lab->labmeta->special2 != '')
                                <li>{{$lab->labmeta->special2}}</li>
                            @endif
                            @if($lab->labmeta->special3 != '')
                                <li>{{$lab->labmeta->special3}}</li>
                            @endif
                            @if($lab->labmeta->special4 != '')
                                <li>{{$lab->labmeta->special4}}</li>
                            @endif
                            @if($lab->labmeta->special5 != '')
                                <li>{{$lab->labmeta->special5}}</li>
                            @endif
                        </ul>
                    @endif

                    @if($zertImages->count() or file_exists(getcwd().'/img/zertifikate/zert1'.$lab->id.'_neu.jpg') or file_exists(getcwd().'/img/zertifikate/zert2'.$lab->id.'_neu.jpg') or file_exists(getcwd().'/img/zertifikate/zert3'.$lab->id.'_neu.jpg'))
                        <div class="info-box">
                            <div class="info-box-header">
                                <strong>Zertifikate:</strong>
                            </div>
                            <div class="info-box-content">
                                <div class="row small-up-1 medium-up-5">
                                    @foreach($zertImages as $image)
                                        <div class="column">
                                            <img src="{{ $image->url }}" alt="">
                                        </div>
                                    @endforeach
                                    @if(file_exists(getcwd().'/img/zertifikate/zert1'.$lab->id.'_neu.jpg'))
                                        <div class="column">
                                            <img src="{{ URL::to('/') }}/img/zertifikate/zert1{{$lab->id}}_neu.jpg"
                                                 alt="">
                                        </div>
                                    @endif
                                    @if(file_exists(getcwd().'/img/zertifikate/zert2'.$lab->id.'_neu.jpg'))
                                        <div class="column">
                                            <img src="{{ URL::to('/') }}/img/zertifikate/zert2{{$lab->id}}_neu.jpg"
                                                 alt="">
                                        </div>
                                    @endif
                                    @if(file_exists(getcwd().'/img/zertifikate/zert3'.$lab->id.'_neu.jpg'))
                                        <div class="column">
                                            <img src="{{ URL::to('/') }}/img/zertifikate/zert3{{$lab->id}}_neu.jpg"
                                                 alt="">
                                        </div>
                                    @endif
                                    @if(file_exists(getcwd().'/img/zertifikate/zert4'.$lab->id.'_neu.jpg'))
                                        <div class="column">
                                            <img src="{{ URL::to('/') }}/img/zertifikate/zert4{{$lab->id}}_neu.jpg"
                                                 alt="">
                                        </div>
                                    @endif
                                    @if(file_exists(getcwd().'/img/zertifikate/zert5'.$lab->id.'_neu.jpg'))
                                        <div class="column">
                                            <img src="{{ URL::to('/') }}/img/zertifikat/zert5{{$lab->id}}_neu.jpg"
                                                 alt="">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer')

@endsection

@section('foot')
    <script>
        $(document).ready(function () {
            $('#onWeekend').change(function () {
                if (this.checked)
                    $('#weekendTimes').fadeIn('slow');
                else
                    $('#weekendTimes').fadeOut('slow');
            });
        });
    </script>
@stop