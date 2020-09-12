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
							<h1>Padento-Labor werden</h1>
							<p class="lead">Sie sind auch ein Labor, welches außergewöhnlichen Service für Patienten leistet? Padento erfreut sich wachsender Beliebtheit, wodurch wir stets auf der Suche nach neuen Partnerlaboren sind.
							<h4>Haben Sie Interesse? Wir beraten Sie gerne und würden uns freuen, Sie kennen zu lernen.</h4>
						</div>
					</div>
				</header>
				<div class="entry-content" style="padding-top:40px;">
					<div class="row">
						<div class="medium-6 columns">
							<h5>Kontaktanfrage</h5>
							{{ Form::open(array('route' => 'request', 'method' => 'POST')) }}
								<div class="error" style="color:red;">
									@if(Session::has('errors'))
										{{ucfirst(Session::get('errors')->first('laborname')) }}
									@endif
								</div>
								<p>
									{{ Form::label('laborname', 'Name des Labors:') }}
									{{ Form::text('laborname', NULL, ['required']) }}
								</p>
								<div class="error" style="color:red;">
									@if(Session::has('errors'))
										{{ucfirst(Session::get('errors')->first('kontaktperson')) }}
									@endif
								</div>
								<p>
									{{ Form::label('kontaktperson', 'Vor- und Nachname:') }}
									{{ Form::text('kontaktperson', NULL, ['required']) }}
								</p>
								<div class="error" style="color:red;">
									@if(Session::has('errors'))
										{{ucfirst(Session::get('errors')->first('mail')) }}
									@endif
								</div>
								<p>
									{{ Form::label('mail', 'E-Mail Adresse:') }}
									{{ Form::email('email', NULL, ['required']) }}
								</p>
								<div class="error" style="color:red;">
									@if(Session::has('errors'))
										{{ucfirst(Session::get('errors')->first('tel')) }}
									@endif
								</div>

								<p>
									{{ Form::label('tel', 'Telefonnummer:') }}
									{{ Form::tel('tel', NULL, ['required']) }}
								</p>

								<p>
									<label>
										<input type="checkbox" name="dsgvo" value="1" required="required" style="margin:0;">
										{!! config('padento.dsgvo.accepted_text') !!}
									</label>
								</p>

								<p>
									{{ Form::button('Abschicken', array(
										'type' => 'submit',
										'class' => 'button secondary radius',
									)) }}
								</p>
							{{ Form::close()}}
						</div>
						<div class="medium-6 columns">
							<h5>Ihre Ansprechpartner</h5>
							<div class="row">
								<div class="small-6 columns">
									<p><img src="<?php URL::to('/') ?>img/RainerEhrich.jpg" />
									<p><strong>Rainer Ehrich</strong></p>
								</div>
								<div class="small-6 columns">
									<p><img src="<?php URL::to('/') ?>img/MarinaEhrich.jpg" />
									<p><strong>Marina Ehrich</strong></p>
								</div>
							</div>

							<p>Alternativ schreiben Sie uns eine Mail an:<br>

							<a href="mailto:info@padento.de">info@padento.de</a><br>

							<p>Oder Rufen Sie uns an unter:<br>

							<a href="tel:+495141360010">+49 5141 - 36 0010</a><br>

						</div>
					</div>
				</div>
			</article>
		</div>
	</div>
</main>
@stop