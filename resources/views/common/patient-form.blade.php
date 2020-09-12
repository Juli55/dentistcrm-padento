<div class="padento-box">
    <div class="padento-box-head">
        {!! $formData['heading']  !!}
    </div>
    <div class="padento-box-content">
        <div class="box-wrap">
            {{-- Das Patienten-Kontakt-Formular --}}

            {!! Form::open(array('url'=>'danke','method'=>'post', 'class' => 'padento-form')) !!}

            @if(Session::has('direct'))
                {{ Form::hidden('direct',  Session::get('direct') ) }}
            @endif

            {!! Form::hidden('lang', $lang) !!}

            <p>
            @if (! $errors->isEmpty())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="error alert alert-danger"
                                style="color: red; font-weight: bold">{{ ucfirst($error) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p>
                {!! Form::text('plz', '', [
                  'placeholder' => 'Postleitzahl',
                  'title' => 'Bitte geben Sie Ihre Postleitzahl hier ein.',
                  'required' => 'true',
                  'autocomplete' => 'off'
                  ]) !!}
            </p>

            <div class="row" style="padding-bottom:4px;">
                <div class="small-4 columns">
                    <strong>Anrede: </strong>
                </div>
                <div class="small-4 columns">
                    <label>
                        {!! Form::radio('salutation', 'Frau', null, ['required' => 'true']) !!} Frau
                    </label>
                </div>
                <div class="small-4 columns">
                    <label>

                        {!! Form::radio('salutation', 'Herr', null) !!} Herr
                    </label>
                </div>
            </div>

            <p>
                {!! Form::text('name', '', [
                    'placeholder' => 'Name',
                    'title' => 'Bitte geben Sie hier Ihren Namen ein',
                    'required' => 'true'
                   ]) !!}

            </p>
            <p>
                {!! Form::email('mail', '', [
                    'placeholder' => 'E-Mail-Adresse',
                    'title' => 'Bitte geben Sie Ihre E-Mail Adresse hier ein.',
                    'required' => 'true'
                   ]) !!}
            </p>
            <p>
                {!! Form::text('tel',  '', [
                    'placeholder' => 'Telefonnummer',
                    'title' => 'Bitte geben Sie Ihre Telefonnummer hier ein.'
                   ]) !!}
            </p>
            {!! $formData['text_below_form'] !!}
            <p>
                <label>
                    <input type="checkbox" name="dsgvo" value="1" required="required" style="margin:0;">
                    {!! $formData['dsgvo_terms'] !!}
                    {{--                    {!! config('padento.dsgvo.accepted_text') !!}--}}
                </label>
            </p>
            <p style="padding-top:15px;margin-bottom:0.5em;" class="centered">
                {!! Form::button($formData['button_text'], [
                    'class' => 'button secondary expanded submit-button',
                    'type'  => 'submit',
                   ]) !!}
            </p>

            {!! $formData['footer_text'] !!}

            {!! Form::close() !!}

        </div>
    </div>
</div>
