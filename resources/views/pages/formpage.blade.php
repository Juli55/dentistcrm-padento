@extends('layouts.home')

@section('head')
    <style>
        h1 {
            color:#5e5e5e !important;
            font-weight: bold !important;
        }
        .shrink {
            background-color: white !important;
            background: white;
            box-shadow: 0 1px 6px 0 rgba(32,33,36,0.28);
        }
        nav.top-bar {
            background-color: #d6ede7;
        }
        header h3 {
            color: #5e5e5e;
        }
    </style>
@stop


@section('content')
    <div class="row" id="form-section">
        <div class="columns"></div>
        <div class="medium-5 columns" style="">
            @include('common.patient-form', ['lang' => $lang, 'formData'=> $formData, 'formName' => 'form2'])
        </div>
        <div class="columns"></div>
    </div>
@endsection
