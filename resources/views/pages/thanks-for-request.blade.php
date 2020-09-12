@extends('layouts.home')

@section('head')

@stop



@section('content')

<main id="page" class="home">
	<div class="row">
		<div class="small-12 columns">
			<article>
				<header class="entry-header">
					<div class="row">
						<div class="small-12 columns">
							<h1>Vielen Dank!</h1>
						</div>
					</div>
				</header>
				<div class="entry-content">
					<p class="centered">{{ $kontaktperson }}, vielen Dank für Ihr Interesse. Sie werden schnellstmöglich von uns hören.
				</div>
			</article>
		</div>
	</div>
</main>
@stop