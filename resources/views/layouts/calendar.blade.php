<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Padento.de
        @if(isset($title))
            – {{ $title }}
        @endif
    </title>
    @yield('head')
    <style>
        .modal-dialog {
            z-index: 9999;
        }
    </style>

    <!--link rel="stylesheet" href="/css/app.css" /-->
    <!-- Latest compiled and minified CSS -->
    <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"-->

</head>
<body>
<div id="app">
    {{--      @include('common.calendar-sidebar') --}}
    <div id="calendar"></div>
    @yield('content')
</div>
<!--script type="text/javascript" src="{{ URL::asset('js/all.js') }}"></script-->
<!--script type="text/javascript" src="{{ URL::asset('js/bundle.js') }}"></script-->

@yield('foot')

</body>
</html>
