<div>
	<div>
		<header id="masthead">
			<div class="row" id="link">
				<div class="small-12 columns">
					<div class="header-wrap">
						<div class="logo" style="height: 84px !important;">
							<a href="{{ url('/') }}">
								<img src="https://padento.de/img/logo.png" alt="Padento - Kostenlose Beratung Zahnersatz">
							</a>
						</div>
						<div class="burger"></div>
						<nav class="navigation" style="height: 100vh !important">
							<button class="close-button" aria-label="Close alert" type="button">
								<span aria-hidden="true">&times;</span>
							</button>
							<ul>
								@foreach(\App\Link::orderBy('sort')->get() as $link)
									<!-- if link is equal to a number  -->
									@if($link->title == '✆ 05141 9780976' && isset($lab))
										@if(Request::is('labor/*') || Request::is('mailtoken/*'))
												<a href="tel:{{ $lab->labmeta->tel }}">✆ {{ $lab->labmeta->tel }}</a>
										@else
												<li>
													<a href="{{ $link->url }}">{{ $link->title }}</a>
												</li>
										@endif
									@else
										<li>
											<a href="{{ $link->url }}">{{ $link->title }}</a>
										</li>
									@endif
								@endforeach
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</header>
		<!--
		@if( AB::getCurrentTest() == 'versionAVideo' ||  AB::getCurrentTest() == 'versionBTopFormWithSubHeader')
			<div  data-sticky-container>
				<div class="sticky" data-sticky style="width: 100%;"  data-options="marginTop:0;stickyOn:small;topAnchor: page; stickyClass: shrink"">
					<nav class="top-bar" data-topbar role="navigation" data-options="sticky_on: large">
						<div class="row">
							<div class="small-12 centered column padding-top-3" style="padding-top: 32px;">Sie brauchen guten <strong class="green">Zahnersatz</strong> ? <a style="margin-top: 12px;"
										href="#form-section" id="go-to-form" class="button secondary submit-button" title="anmeldeformular">Anmelden</a></div>
						</div>
					</nav>
				</div>
			</div>
		@endif -->
    </div>
</div>
