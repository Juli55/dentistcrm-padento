@extends('layouts.home')

@section('head')

@stop

@section('content')
    <main id="page">
        <div class="row">
            <div class="medium-10 column small-centered">
                @if(isset($request))
                    <h1 class="centered">Vielen Dank!</h1>
                    <p> Wir haben Ihren Wunschtermin erhalten und werden uns in den kommenden Tagen in der von Ihnen
                        angegeben Zeit von {{$request->from}} bis {{$request->till}}
                        @if ($request->weekend_from != '' && $request->weekend_till != '')
                            oder am Samstag zwischen {{$request->weekend_from}} und {{$request->weekend_till}}
                        @endif
                        telefonisch bei Ihnen zur Abstimmung eines Besuchstermines für ein ausführliches, kostenloses
                        Beratungsgespräch im Dentallabor melden.
                    </p>
                @else
                    <h1 class="centered">Vielen Dank für Ihr Vertrauen! <br>Sie haben alles richtig gemacht!</h1>
                    <section class="your-lab">
                        <div class="row">
                            <div class="medium-8 columns">
                                <h2>So geht es für Sie jetzt weiter:</h2>
                                <p>Es ist für Sie nur noch <b>ein kleiner Schritt <br/>zu Ihren schönen und neuen Zähnen:</b> Rufen Sie <b><u>jetzt</u></b> in ihrem Dentallabor an und vereinbaren Sie einen <u>kostenfreien Beratungstermin</u>
                                    <a href="tel:{{ $labtel  }}">{{ $labtel }}</a></p>
                                <p>Denn wenn Sie neuen Zahnersatz benötigen, gehen Sie mit Padento den besten und sichersten Weg, den es gibt.</p>
                                <p>Ihr Ansprechpartner heißt {{ $kontaktperson }} und freut sich auf Sie!</p>
                            </div>
                            <div class="medium-4 columns">
                                <div class="callout">
                                    <div class="row">
                                        <div class="medium-12 columns">
                                            <p class="centered"><img src="/{{ $image }}"/>
                                        </div>
                                        <div class="medium-12 columns">
                                            <p class="centered"><img src="/img/kontaktfotos/foto{{ $id }}_neu.jpg"/>
                                            <p><strong>Ihr Ansprechpartner:</strong>
                                                <br>{{ $kontaktperson }}
                                                <br>Dentallabor aus {{ $stadt }}
                                                <br>Telefon: {{ $labtel }}
                                                <br><br><a class="button secondary" href="/labor/{{ $labor }}">Mein
                                                    Dentallabor ansehen</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>

    </main>


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
    {{--
    <!-- Google Analytics -->
    <script>
     ga('send', 'event', 'Kontakt', 'bestätigt', '{{ Users::find($lab->account_id)->realname }}');
    </script>
    <!-- End Google Analytics -->
    --}}

@stop
