@extends('layouts.default')

@section('head')

  <link rel="stylesheet" href="/css/app.css" />
  <meta name="csrf-token" id="token" value="{{ csrf_token() }}" />
@stop

@section('content')

  <div id="app">

    @include('common.admin-sidebar')

  </div>

@stop

@section('foot')
@stop
