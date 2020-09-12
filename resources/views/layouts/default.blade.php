<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8"/>
    <title>Padento.de
      @if(isset($title))
        – {{ $title }}
      @endif
    </title>
    <link rel="stylesheet" href="{{ URL::asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/summernote.css') }}">

    <script>
        window.Application = <?php echo json_encode(array_merge(
            Application::scriptVariables(), []
        )); ?>
    </script>

    @yield('head')

  </head>
  <body>

    @yield('content')

    <script type="text/javascript" src="{{ URL::asset('js/all.js').'?'.time() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/bundle.js').'?'.time() }}"></script>
    <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script>
    @yield('foot')
    <!--script src='//cdn.tinymce.com/4/tinymce.min.js'></script-->

  <div id="debug">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div class="debugged-content"></div>
  </div>
  <div id="spinner">
      <i class="fa fa-spinner" aria-hidden="true"></i>
  </div>
  </body>
</html>
