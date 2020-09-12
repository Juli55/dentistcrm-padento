<footer id="colo">
	<div class="row">
		<div class="medium-6 columns" style="padding-top:20px;">
			<!--
			<h4>Padento</h4>
			<p>Mit einem Dentallabor an Ihrer Seite finden Sie Zahnersatz, der zu Ihnen passt. Padento bringt Patienten und Dentallabore zusammen. Nutzen Sie die Beratung aus zwei Perspektiven, die des Zahnarztes und des Zahntechnikers. Zahnersatz neu gedacht.</p>
			-->
			<h4 style="font-weight:bold;">Sie haben Fragen oder benötigen Hilfe?</h4>
			<p>
				Wir sind für Sie da! Montag bis Freitag von 09-17 Uhr.

				<br>Rufen Sie uns einfach an: @if((Request::is('labor/*') || Request::is('mailtoken/*')) && isset($lab))<a href="tel:{{ $lab->labmeta->tel }}">{{ $lab->labmeta->tel }}</a>@else<a href="tel:+49051419780976">+49 05141-9780976</a>@endif
			</p>
		</div>


		<!--
		<div class="medium-4 columns">

			<h4>Unser Prinzip</h4>
			<p class="entered"><a href="//fast.wistia.net/embed/iframe/u3t9qiwm1t?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/54aff38af80787af158f5f1f1609f5aefff3dd0d.jpg?image_play_button=true&amp;image_play_button_color=00aff5e0&amp;image_crop_resized=150x84" alt=""></a></p>
			<p><a href="//fast.wistia.net/embed/iframe/93im63n76x?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]">Video</a>

			<h4>Unser Prinzip</h4>
			<p class="entered"><a href="//fast.wistia.net/embed/iframe/u3t9qiwm1t?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/54aff38af80787af158f5f1f1609f5aefff3dd0d.jpg?image_play_button=true&amp;image_play_button_color=00aff5e0&amp;image_crop_resized=150x84" alt=""></a></p>
			<p><a href="//fast.wistia.net/embed/iframe/93im63n76x?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]">Video</a>
		</p>
		</div>
		-->




		<div class="medium-4 columns" style="padding-top:20px; padding-bottom:30px;">
			<h4 style="font-weight:bold;">Für Labore:</h4>
			<!-- <p><strong>Als Labor finden Sie Padento gut?</strong></p>
			<div class="row">
				<div class="small-6 columns">
					<p><a href="/als-labor-mitmachen" class="button secondary expanded">Mitmachen!</a></p>
				</div>
				<div class="small-6 columns">
					@if (Auth::user())
						<p><a href="/app/dashboard" class="button expanded">Loginbereich</a>
					@else
						<h5>Sind Sie bereits Mitglied?</h5>
						<p><a href="/login">Zum Login!</a>
					@endif
				</div>
			</div>

			<p>Telefonnummer für Labore:<br>
			<a href="tel:+495141360010">+49 5141 - 36 0010</a></p>
			-->


			@if (Auth::user())
				Du bist bereits eingeloggt<br>
				<a href="/app/dashboard">Zum Loginbereich!</a>
				<!-- <p><a href="/app/dashboard" class="button expanded">Loginbereich</a> -->
			@else
				Sie sind bereits Mitglied?<br>
				<a href="/login">Zum Login!</a>
			@endif

			<p style="margin-top: -15px !important;">
			<br>Sie möchten Padento kennenlernen?<br>
			<a href="https://padento.de/als-labor-mitmachen">Jetzt Partner werden!</a>
			</p>

		</div>
	</div>
	<div class="row">
		<div class="small-12 columns">
			<nav class="meta-nav">
				<ul>
					<li><a href="/impressum">Impressum</a></li>
					<li><a href="/datenschutzerklaerung">Datenschutzerklärung</a></li>
					<li>© Padento {{ date('Y') }}. Alle Rechte vorbehalten</li>
				</ul>
			</nav>
		</div>
	</div>
</footer>
<script src="//fast.wistia.net/assets/external/iframe-api-v1.js"></script>