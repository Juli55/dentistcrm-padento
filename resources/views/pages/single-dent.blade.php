<div id="page" class="lab">
    <header class="lab-header" style="background-color:#EDF7F1 !important; padding-bottom: 25px;margin-bottom:1.3rem !important;">
        <div class="row">
            <div class="medium-10 medium-centered columns">
                <div class="lab-header-content">
                    <h1 style="color:#4cace5;margin-bottom:1em;">{{ $lab->name }}</h1>
                    <div class="row" data-equalizer>
                        <div class="column medium-4">
                            <div class="contact" data-equalizer-watch style="background-color: white; border-radius:12px;">
                                <div class="pic-header" style="padding:1em;background-color:#4cace5;color:white;border-radius:12px 12px 0px 0px;margin-bottom:0px;">
                                    <strong>Ihr Ansprechpartner</strong>
                                </div>
                                <div class="contact-person" style="background-color:white;border-radius:0px 0px 12px 12px; margin-top:0px;margin-bottom:0px;display:block;">
                                    <div class="avatar text-center">
                                        <img style="margin:1em 0em 1em 0em;max-height:15em;" src="{{ URL::to('/') }}/img/foto{{ $lab->account_id }}_200small.jpg" alt="foto" style="height:auto;">
                                    </div>
                                    <div style="padding: 0 20px 20px 20px;" class="contact-person-name"><h4>{{ $lab->dentmeta->contact }}</h4></div>
                                </div>
                            </div>
                        </div>
                        <div class="column medium-8">
                            @if(file_exists($logo))
                            <div class="lablogo text-center" data-equalizer-watch style="background-color: white; border-radius:12px; padding:20px;">
                                <img style="margin-bottom:2em;" src="{{ URL::to('/') }}/{{ $logo }}" alt="{{ $lab->name }}"/>
                                <p class="text-left">{{ $lab->dentmeta->hello }}
                            </div>
                            @else
                            <div class="lablogo text-center" data-equalizer-watch style="background-color: white; border-radius:12px; padding:20px;">
                                <img style="margin-bottom:2em;" src="{{ URL::to('/') }}/img/informieren_made_in_ger.svg" alt="{{ $lab->name }}"/>
                                <p class="text-left">{{ $lab->dentmeta->hello }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="row">
        <div class="medium-10 medium-centered columns">
            <div class="welcome">
                <p class="lead">
                {{--str_replace("\n","<p>",$meta->hello)--}}
                </p>
            </div>
            <h3 style="color:#4cace5;">Über uns und unsere Arbeit</h3>
            <p>{{ str_replace("\n","<p>",$lab->dentmeta->text) }}</p>
            @if(file_exists(getcwd().'/img/bild1'.$lab->id.'_200small.jpg'))
            <div class="bilder-wrap" style="background-color:#d9f2e5;border-radius:12px 12px 0px 0px;padding:1em;">
                <b>Bilder:</b>
            </div>
            <div class="bilder" style="border:solid 1px #d9f2e5;margin-bottom:2em;padding:2em;">
                <p>
                <ul class="clearing-thumbs medium-block-grid-3" {{--data-clearing--}}>
                    @if(file_exists(getcwd().'/img/bild1'.$lab->dentmeta->dent_id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild1{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild1{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                    @if(file_exists(getcwd().'/img/bild2'.$lab->id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild2{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild2{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                    @if(file_exists(getcwd().'/img/bild3'.$lab->id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild3{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild3{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                    @if(file_exists(getcwd().'/img/bild4'.$lab->id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild4{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild4{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                    @if(file_exists(getcwd().'/img/bild5'.$lab->id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild5{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild5{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                    @if(file_exists(getcwd().'/img/bild6'.$lab->id.'_200small.jpg'))
                    <li>{{--<a href="/img/bild6{{$meta->id}}_200small.jpg">--}}<img src="{{ URL::to('/') }}/img/bild6{{$meta->dent_id}}_200small.jpg" alt=""></a></li>
                    @endif
                </ul>
            </div>
            @endif
            <div class="services">
            </div>
        </div>
    </div>
    <div id="formular" class="row">
        <div class="medium-12 medium-centered column">
            <div class="column medium-centered medium-10">
                <div class="row" style="background-color:#d9f2e5;padding:1em;border-radius:12px 12px 0px 0px;">
                    <h4 class="column"><strong>{{ $lab->name }}</strong></h4>
                    <div class="column medium-6">
                        <p class="">{{ $lab->dentmeta->street }}
                        <br/>{{ $lab->dentmeta->city }}</p>
                    </div>
                    <div class="column medium-6">
                        <p>Email: {{ $lab->user->email }}
                        <br/>Telefon: {{ $lab->dentmeta->tel }}
                    </div>
                </div>
                {{--
                <div class="row">
                    <div class="column" style="background-color:#edf7f1;padding:2em 1em 0em 1em;border-radius:0px 0px 12px 12px;">
                        {{ Form::open(array('url'=>'praxis/'.$meta->dent_id.'/post','method'=>'POST')) }}
                        <div class="errors">
                        </div>
                        <div class="error" style="color:red;">
                            @if(Session::has('errors'))
                            {{ucfirst(Session::get('errors')->first('name'))}}
                            @endif
                        </div>
                        <div class="column medium-8 medium-centered">
                            {{ Form::label('name','Vorname Nachname')}}
                            {{ Form::input('text','name', '',[ 'placeholder' => 'Ihr Vor- und Nachname', 'required' => 'true' ]) }}
                        </div>
                        <div class="error"style="color:red;">
                            @if(Session::has('errors'))
                            {{ucfirst(Session::get('errors')->first('email'))}}
                            @endif
                        </div>
                        <div class="large-8 columns medium-centered">
                            {{ Form::label('email','E-Mail Adresse') }}
                            {{ Form::input('email','email','', ['placeholder' => 'Ihre Email-Adresse (user@example.com)']) }}
                        </div>
                        <div class="error"style="color:red;">
                            @if(Session::has('errors'))
                            @if(Session::get('errors')->first('msg')=='msg muss ausgefüllt sein.')
                            Sie müssen eine Nachricht eingeben.
                            @endif
                            @endif
                        </div>
                        <div class="column medium-8 medium-centered">
                            {{ Form::label('msg','Bitte schildern Sie kurz Ihr Anliegen') }}
                            {{ Form::textarea('msg','', ['placeholder' => 'Ihre Nachricht']) }}
                        </div>
                        <p class="centered">{{ Form::button('Abschicken', array(
                        'type' => 'submit',
                        'class' => 'radius secondary large-button medium-6 medium-centered',
                        )) }}
                        </p>
                        {{ Form::close() }}
                    </div>
                </div>--}}
            </div>
        </div>
    </div>

@if(Session::has('who'))
<div class="row">
    <div class="column small-12">
        <p>Wann passt es Ihnen am besten?
    </div>
    <div class="column large-3">
        {{ Form::open(array('url' => '/wann', 'method' => 'POST')) }}
        {{ Form::hidden('who',Session::get('who')) }}
        {{ Form::hidden('labor',$dent->users->realname) }}
        {{ Form::hidden('labmail', Account::find($dent->account_id)->email) }}
        {{ Form::label('zeiten','Erreichbar werktags von:') }}
        {{ Form::select('zeiten', array(
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
        '17:00Uhr' => '17:00Uhr'
        ), null, ['required']) }}
    </div>
    <div class="column large-3">
        {{ Form::label('zeiten2','bis:') }}
        {{ Form::select('zeitenbis', array(
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
        '18:00Uhr' => '18:00Uhr'
        ), null, ['required']) }}
    </div>
    <div class="column large-4">
        {{ Form::submit('abschicken',array('class' => 'button secondary radius expand tiny', 'style' => 'margin-top:1.5rem;')) }}
    </div>
</div>
@endif
@if(isset($test))
<!-- ''''BBBLLLUUUPP'''' -->
@endif
{{--
<a href="#" data-reveal-id="firstModal" class="reveal-link"></a>
<!-- Reveal Modals begin -->
<div id="firstModal" class="reveal-modal" data-reveal>
    <h2>Padento auf Facebook</h2>
    <div class="row">
        <div class="medium-6 columns">
            <div class="fb-like-box" data-href="https://www.facebook.com/padento" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
        </div>
        <div class="medium-6 columns">
            <p class="lead">Folgen Sie uns auf Facebook und erhalten Sie Informationen rund um das Thema Zahnersatz und gesunde Zähne.</p>
            <div class="fb-like" data-href="https://facebook.de/padento" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
            <p>Kennen Sie schon unseren Blog?</p>
            <p><a href="https://padento.de/wissen/blog" class="radius button large secondary expand">Zum Blog</a></p>
        </div>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>
--}}
<script>
setTimeout(function () {
$('a.reveal-link').trigger('click');
}, 5000);
{{--$(document).ready(function(){
$('a.reveal-link').delay(1000).trigger('click');
});--}}
</script>
