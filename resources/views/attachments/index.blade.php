@extends('layouts.home')

@section('content')
    <main id="page">
        <div class="row">
            <div class="medium-12 column">

                <h1>Dokumente hochladen</h1>
                @if(session()->has('success'))
                    <p><strong>{{ session('success') }}</strong></p>
                @endif
                <p>Hier können Sie uns Dokumente wie den Heil- und Kostenplan zukommen lassen.</p>
                <p>
                    <small><strong>Achtung:</strong> Erlaubt sind nur Bilder und PDF-Dateien.</small>
                </p>

                <form action="/attachments/upload/{{ $token }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="file" name="attachment">

                    @if (count($errors) > 0)
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <button type="submit" class="button primary" style="color: white;">Jetzt hochladen</button>

                </form>
            </div>
        </div>
    </main>
@endsection
