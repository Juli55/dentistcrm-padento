@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="padento-login">
                <img src="/images/logo.png" alt="">

                @if (isset($_GET['user']))
                    <div class="alert alert-warning" role="alert">Ihr Padento-Profil wurde deaktiviert. Sollte es sich dabei um ein versehen handeln, melden Sei sich bitte bei <a href="mailto:info@padento.de">info@padento.de</a></div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Anmelden
                    </div>
                    <div class="panel-body">
                        <form class="form" role="form" method="POST" action="{{ url('/login') }}">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label class="control-label">Ihre E-Mail-Adresse:</label>

                                <div class="">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="control-label">Ihr Passwort</label>

                                <div class="">
                                    <input type="password" class="form-control" name="password">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> An diesem Rechner angemeldet bleiben
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-sign-in"></i> Anmelden
                                    </button>

                                    <a class="btn btn-link" href="{{ url('/password/reset') }}">Passwort vergessen?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
