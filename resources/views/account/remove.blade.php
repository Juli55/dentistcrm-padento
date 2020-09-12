@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top: 40px">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <img src="/images/logo.png" alt="" style="width: 300px; margin-bottom: 40px;">

                @if($msg = session('msg'))
                    <div class="alert alert-success"><p>{{ $msg }}</p></div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading">Meine Daten bei Padento löschen</div>

                    <div class="panel-body">
                        <form action="{{ route('account.remove.send-confirmation') }}" method="POST">
                            {!! csrf_field() !!}

                            <p>Bitte geben Sie Ihre bei Padento verwendete E-Mail-Adresse ein. Sie erhalten im Anschluss
                                eine E-Mail in der Sie das Löschen Ihrer Daten bestätigen müssen.</p>

                            <p>Wir erhalten dann Ihre Informationen und werden Ihre Daten löschen.</p>

                            <div class="form-group">
                                <label>E-Mail-Adresse</label>
                                <input type="email" name="email" class="form-control" placeholder="Ihre E-Mail-Adresse"
                                       required>
                                @if ($errors->has('email'))
                                    <span class="help-block"
                                          style="color: darkred;"><strong>{{ $errors->first('email') }}</strong></span>
                                @endif
                            </div>

                            <div class="form-group">
                                <button class="btn btn-danger">Löschung meiner Daten beantragen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
