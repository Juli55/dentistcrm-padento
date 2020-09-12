<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" href="/css/foundation.min.css"/>
    <title>Padento.de
        @if(isset($title))
            – {{ $title }}
        @endif
    </title>
    @yield('head')

</head>
<body>
<div class="row">
    <div class="col col-md-12">
        @yield('content')
    </div>
</div>
<script src="/js/all.js"></script>
<script src="/js/bundle.js"></script>

@yield('foot')
<!--script src='//cdn.tinymce.com/4/tinymce.min.js'></script-->


</body>
</html>
