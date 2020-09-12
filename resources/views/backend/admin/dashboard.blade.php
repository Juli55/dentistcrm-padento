@extends('layouts.default')

@section('head')
  <meta name="csrf-token" id="token" value="{{ csrf_token() }}" />
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6-c7aiyiq-P2ByG3M8vPlN_K4iXlO15I"></script>
  <link rel="stylesheet" href="/css/daterangepicker.css">

  <style>
    .note-group-select-from-files {
      display: none;
    }
  </style>
@stop

@section('content')

  <div id="app">

    @include('common.admin-sidebar')

  </div>

@stop

@section('foot')
@stop
