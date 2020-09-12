@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top: 40px">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <img src="/images/logo.png" alt="" style="width: 300px; margin-bottom: 40px;">

                @if($msg = session('msg'))
                    <div class="alert alert-success"><p>{{ $msg }}</p></div>
                @endif
            </div>
        </div>
    </div>
@endsection
