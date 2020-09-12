@extends('layouts.home')

@section('head')

@stop

@inject('settings', 'App\Services\Setting')

@section('content')
    <main id="page">
        <div class="row">
            <div class="medium-10 column small-centered">
                <h1 class="centered">{{ $settings->getNoLabFoundTitle() }}</h1>
                <section class="your-lab">
                    <div class="row">
                        <div class="medium-8 medium-centered columns">
                            {!! $settings->getNoLabFoundBody() !!}
                        </div>
                    </div>
                </section>
            </div>
        </div>

    </main>


@endsection

@section('foot')
  {{--
  <!-- Google Analytics -->
  <script>
   ga('send', 'event', 'Kontakt', 'bestätigt', '{{ Users::find($lab->account_id)->realname }}');
  </script>
  <!-- End Google Analytics -->
  --}}
@stop
