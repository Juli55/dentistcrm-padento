@extends('layouts.home')

@section('head')

@stop



@section('content')
  @if(Session::has('nocontact'))
    {{ Session::forget('nocontact') }}
  @endif

  <main id="page" class="home">
	  <div class="row">
	  	<div class="small-12 columns">
			<article>
				<header class="entry-header">
					<div class="row">
						<div class="small-12 columns">
							<h1>Zahntechnikermeister helfen Ihnen beim Thema Zahnersatz</h1>

							<p class="centered welcome lead">Wir haben <strong>über 14.800 Menschen kennengelernt, die unzufrieden mit dem Ablauf bei Ihrem Zahnarzt und unzureichend über die Möglichkeiten aufgeklärt worden sind.</strong> Andere wollen einfach eine zweite Meinung und mehr Sicherheit bei ihrer Entscheidung. Viele lassen sich deshalb <strong>aus zwei Perspektiven beraten: Vom Zahnarzt und vom Zahntechniker</strong>.</p>
						</div>
					</div>
				</header>
				<div class="entry-content">
					<section>
						<div class="row">
							<div class="small-12 columns">

								<h2>Finden Sie Ihren besten Zahnersatz:</h2>
								<h3>Fragen, die immer wieder auftauchen:</h3>
								<ul>
									<li>Zeigt der Zahnarzt mir überhaupt <strong>alle Möglichkeiten</strong> und vor allem die für mich besten?</li>
									<li>Erhalte ich einen <strong>fairen Preis</strong>?</li>
									<li>Wird mein neuer Zahnersatz auch<strong> in einem deutschen Meisterlabor</strong> hergestellt?</li>
									<li>Wo bekomme ich eigentlich eine <strong>zweite, unabhängige Meinung?</strong></li>
								</ul>

								<h3>Mit Selbstbewusstsein Lachen, Sprechen und Essen:</h3>

								<ol>
									<li>Wir sind der Überzeugung, dass Zahnersatz <strong>nur in Begleitung des Zahntechnikers</strong> richtig gut werden kann.</li>
									<li>Unsere Partner sind ausgewählte Dentallabore und Zahnärzte aus</strong> <strong>ganz</strong> Deutschland.</strong></li>
									<li>Wir sind <strong>kein</strong> Billiganbieter und dennoch gibt es <strong>immer eine Lösung</strong>.</li>
									<li>Mit der Unterstützung des Zahntechnikermeisters bekommen Sie Infos vom <strong>Hersteller.</strong> Er ist der <strong>Profi für Zahnersatz.</strong></li>
									<li>Obwohl die <strong>Beratung sehr wertvoll</strong> für den Patienten ist, bieten wir diese <strong>kostenlos</strong> an.<br>
									</li>
								</ol>
							</div>
						</div>
					</section>
					<section class="section cta-row">
						<div class="row">
							<div class="medium-7 columns">
								<div class="padento-box">
									<div class="padento-box-head">
										<p><strong class="green">Video:</strong> Schauen Sie sich einfach unser <strong  class="green">Video</strong> an, um in <strong  class="green">1 Minute</strong> mehr zu erfahren.</p>
									</div>
									<div class="padento-box-content">
										<div class="flex-video widescreen">
											<iframe src="//fast.wistia.net/embed/iframe/k41j7v41lk?videoFoam=true" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="720" height="405"></iframe><script src="//fast.wistia.net/assets/external/iframe-api-v1.js"></script>
										</div>
										<div class="box-wrap">
											<h3 style="text-align: center;">Ausgezeichnet</h3>
											<p style="text-align: center;" class="lead">1. Platz für die beste Idee des Jahres 2015</p>
											<p style="text-align: center;"><img src="https://padento.de/img/award2015.png" width="55%" />
										</div>
									</div>
								</div>
							</div>
							<div class="medium-5 columns">
								<div class="padento-box">
									<div class="padento-box-head">
										<p>Jetzt <strong class="green">PLZ eingeben</strong> und ein Dentallabor in Ihrer Nähe finden – <strong class="green">kostenlos</strong> und <strong class="green">unverbindlich</strong>!
									</div>
									<div class="padento-box-content">
										<div class="box-wrap">
											@include('common.patient-form', ['formName' => 'form1'])
										</div>
									</div>
								</div>

							</div>
						</div>
					</section>

					<section class="section">
						<div class="row">
							<div class="small-12 large-8 large-centered columns">
								<h2 class="centered">Der Ablauf:</h2>
								<ol>
									<li>Füllen Sie das <strong>Formular</strong> aus und tragen Sie Ihre Daten dort ein.</li>
									<li>Sie werden von uns telefonisch kontaktiert, um einen <strong>Termin</strong> zu machen.</li>
									<li>Sie gehen <strong>zusammen mit dem Zahntechniker zu einem ausgewählten Zahnarzt</strong>, von dem sich auch der Zahntechniker behandeln lassen würde.</li>
									<li>Ein ausgewählter Zahnarzt ist nicht teurer als andere Zahnärzte. <strong>Er ist einfach nur sehr gut</strong>.</li>
									<li>Sie bekommen zusätzlich eine <strong>zweite Meinung vom Zahnarzt</strong>, die bei den vielen Möglichkeiten heutzutage absolut wichtig ist.</li>
									<li>Der Zahntechniker lernt Sie als <strong>Mensch</strong> persönlich kennen und sieht nicht nur das Gips-Modell.</li>
									<li>Dadurch werden Ihre neuen Zähne <strong>viel besser zu Ihnen passen</strong>.</li>
									<li>Bei <strong>Angstpatienten</strong> werden spezielle Zahnärzte ausgesucht, die sich damit auskennen.</li>
								</ol>
							</div>
						</div>
					</section>

					<section class="videos section">
						<div class="row">
							<div class="small-12 columns">
								<h2 class="centered">Lassen Sie sich Ihre Möglichkeiten zeigen</h2>
							</div>
						</div>
						<div class="row medium-up-2">

							<div class="column">
						            <div class="down box vbox">
							        <p><strong>Brücke aus Vollkeramik:</strong></p>
						            </div>
							    <div class="box1 vbox" style="border-radius: 0px 0px 10px 10px;">
							        <div class="videoWrapper wideScreen">
								    <div class="videoPadding">
									<a href="//fast.wistia.net/embed/iframe/h54vl1zbji?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/81e278dbf15d4a866e433c310b52a0f712ff446b.jpg?image_play_button=true&image_play_button_color=00aff5e0&image_crop_resized=640x360" alt="" /></a>
								    </div>
						                </div>
							    </div>
							</div>

							<div class="column">
						            <div class="down box vbox">
							        <p><strong>Prothese hält besser mit Druckknöpfen:</strong></p>
						            </div>
							    <div class="box1 vbox" style="border-radius: 0px 0px 10px 10px;">
							        <div class="videoWrapper wideScreen">
							            <div class="videoPadding">
									<a href="//fast.wistia.net/embed/iframe/vvjex772rw?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/6845b10174f4d73fb846901de9704e22f6e6fb6f.jpg?image_play_button=true&image_play_button_color=00aff5e0&image_crop_resized=640x360" alt="" /></a>
								    </div>
						                </div>
							    </div>
							</div>

							<div class="column">
						            <div class="down box vbox">
							        <p><strong>Implantat Oberer Schneidezahn:</strong></p>
						            </div>
							    <div class="box1 vbox" style="border-radius: 0px 0px 10px 10px;">
							        <div class="videoWrapper wideScreen">
							            <div class="videoPadding">
									<a href="//fast.wistia.net/embed/iframe/6picq81hza?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/85f7f3e548864b2a55a16d7b0a3de39546614fd6.jpg?image_play_button=true&image_play_button_color=00aff5e0&image_crop_resized=640x360" alt="" /></a>
								    </div>
						                </div>
							    </div>
							</div>

							<div class="column">
						            <div class="down box vbox">
							        <p><strong>Teleskop Prothese im Unterkiefer:</strong></p>
						            </div>
							    <div class="box1 vbox" style="border-radius: 0px 0px 10px 10px;">
							        <div class="videoWrapper wideScreen">
							            <div class="videoPadding">
									<a href="//fast.wistia.net/embed/iframe/ycz6wutj8a?popover=true" class="wistia-popover[height=360,playerColor=00aff5,width=640]"><img src="https://embed-ssl.wistia.com/deliveries/27d335e2a3d653132506d47b68859988f790993d.jpg?image_play_button=true&image_play_button_color=00aff5e0&image_crop_resized=640x360" alt="" /></a>
								    </div>
						                </div>
							    </div>
							</div>
						</div>
					</section>

					<section class="idea section">
						<div class="row">
							<div class="small-12 columns">
								<div class="row">
									<div class="medium-8 medium-push-4 columns">
										<h2>
											Funktion und Ästhetik<br/>bestimmen die Qualität Ihrer Zähne
											<small>Das Padento-Prinzip</small></h2>
			      						</h2>
									</div>
								</div>
								<div class="row">
									<div class="medium-4 columns">
										<p class="centered"><img src="{{ URL::to('/') }}/img/WasistPadento1.svg" alt="Was ist Padento" /></p>
									</div>
									<div class="medium-8 columns">
										<h3>Die Idee</h3>
										<p>Nur zusammen mit einem guten Zahnarzt und einem guten Dentallabor kann der bestmögliche  Zahnersatz hergestellt werden. Zahntechniker erkennen anhand der Abdrücke die Qualität eines Zahnarztes sofort. Zahntechnikermeister informieren Sie zum Thema Zahnersatz nach zahnärztlichen Vorgaben und laden Sie ein in Ihr Dentallabor vor Ort. Das ist ein Erlebnis! Die vielen neuen Techniken kann ein Zahnarzt alleine gar nicht mehr alle zeigen.</p>
									</div>
								</div>
								<div class="row">
									<div class="medium-4 columns">
										<p class="centered"><img src="{{ URL::to('/') }}/img/WasistPadento2.svg" alt=""></p>
									</div>
									<div class="medium-8 columns">
										<h3>Zahnersatz zum Anfassen</h3>
										<p>Damit Sie dieses komplexe Thema im wahrsten Sinne des Wortes „begreifen“ können, vereinbaren Sie jetzt einen Termin in einem Padento Dentallabor in Ihrer Nähe. Danach können Sie bessere Entscheidungen treffen. Schliesslich wird Ihr Zahnersatz vom Zahntechniker und nicht vom Zahnarzt hergestellt. Deshalb sollten Sie sich aus 2 Perspektiven beraten lassen: Vom Zahnarzt <u>und</u> vom Zahntechniker.</p>
									</div>
								</div>
								<div class="row">
									<div class="medium-4 columns">
										<p class="centered"><img src="{{ URL::to('/') }}/img/WasistPadento3.svg" alt=""></p>
									</div>
									<div class="medium-8 columns">
										<h3>Sie haben die Wahl</h3>
										<p>Sie haben die freie Zahnarztwahl und was viele nicht wissen: Sie können sich sogar Ihr Dentallabor aussuchen. Die meisten Menschen gehen nach einer Beratung im Labor zusätzlich zu einem Padento-Zahnarzt, um sich noch eine 2. Zahnarzt-Meinung zu holen. Sicher ist Sicher! Dort finden Sie einen sehr guten Behandler vor.</p>
									</div>
								</div>
								<div class="row">
									<div class="medium-4 columns">
										<p class="centered"><img src="{{ URL::to('/') }}/img/preis.svg" alt=""></p>
									</div>
									<div class="medium-8 columns">
									<h3>Finanzieren Sie Ihre Neuen Zähne</h3>
									<p>Es ist heutzutage völlig normal, schöne und wertvolle Dinge zu finanzieren. Nicht jeder hat ein paar Tausend Euro so einfach übrig. Sie können bei uns ganz einfach und bequem Ihren Eigenanteil finanzieren. Bonität vorausgesetzt. Freuen Sie sich auf Ihre schönen und neuen Zähne. Natürlich nur Made in Germany.</p>
									</div>
								</div>
							</div>
						</div>
					</section>

					<section class="finance section">
						<div class="row">
							<div class="small-12 columns">
								<div class="padento-box">
									<div class="padento-box-content">
										<div class="box-wrap">
											<h4 class="centered">Zahnersatz ist oft eine unvorhergesehene, finanzielle Belastung.
											<strong class="green">Wir bieten Ihnen die Lösung einer Teilzahlung im Dentallabor in wenigen Minuten.</strong> <br>
											Auf Wunsch bis 20.000 €. Bonität vorausgesetzt.</h4>
										</div>
									</div>
								</div>

								<h2 class="centered"><span class="green-tag">97€</span> <span class="green-tag">125€</span> <span class="green-tag">155€</span> oder ganz individuell:</h2>
								<p>
									Ihre spätere Teilzahlung können Sie <strong>sehr flexibel</strong> gestalten. Sie können bis zu <strong>12 Monate zinsfrei</strong> Ihre Raten bezahlen oder auch lange Laufzeiten bis zu 84 Monate in Anspruch nehmen. <strong>Wählen Sie einfach das Passende</strong> für Ihren Geldbeutel:
								</p>

								<div class="row finance-boxes" data-equalizer data-equalize-on="medium">
									<div class="medium-4 columns">
										<div class="padento-box">
											<div class="padento-box-head">
												<h4>Beispiel: 97 €</h4>
											</div>
											<div class="padento-box-content">
												<div class="box-wrap" data-equalizer-watch>
													<p>4.000,00€, die Sie finanzieren möchten = 97€ in 48 Monaten</p>
												</div>
											</div>
										</div>
									</div>
									<div class="medium-4 columns">
										<div class="padento-box">
											<div class="padento-box-head">
												<h4>Beispiel: 125 €</h4>
											</div>
											<div class="padento-box-content">
												<div class="box-wrap" data-equalizer-watch>
													<p>1.500,00€, die Sie finanzieren möchten = 125€ in 12 Monaten (zinsfrei)</p>
												</div>
											</div>
										</div>
									</div>
									<div class="medium-4 columns">
										<div class="padento-box">
											<div class="padento-box-head">
												<h4>Beispiel: 155 €</h4>
											</div>
											<div class="padento-box-content">
												<div class="box-wrap" data-equalizer-watch>
													<p>5.000,00€, die Sie finanzieren möchten = 155€ in 36 Monaten</p>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</section>


					<section class="about section">
						<div class="row">
							<div class="medium-9 medium-push-2 columns">
								<h3>Wer steht hinter Padento?</h3>
							</div>
						</div>
						<div class="row">
							<div class="medium-2 columns">
								<div class="row">
									<div class="small-6 medium-12 columns">
										<p><img src="<?php URL::to('/') ?>img/RainerEhrich.jpg" />
									</div>
									<div class="small-6 medium-12 columns">
										<p><img src="<?php URL::to('/') ?>img/MarinaEhrich.jpg" />
									</div>
								</div>
							</div>
							<div class="medium-10 columns">
								<p><strong>Rainer Ehrich</strong>, Erfinder der TEK-1 Prothese und ehemaliger Laborbesitzer hat die Idee von Padento gehabt, mit dem Wunsch eine bessere Beratungssituation für Menschen zu schaffen, die aktuell oder in naher Zukunft neue Zähne bekommen.
				  				<p>Seine Frau <strong>Marina Ehrich</strong> ist die  „gute Seele“ von Padento und hat immer ein offenes Ohr für die Belange zu diesem wichtigen Thema Zahnersatz. „Sie trägt Ihr Herz an der richtigen Stelle“, hören wir oft von unseren Kunden.
				    			<p>Da Rainer Ehrich die Dentalbranche so gut wie kaum ein anderer kennt, sieht er immer wieder, wie Menschen beim Thema Zahnersatz überfordert sind. Der Zahnarzt als Mediziner kann die mittlerweile so vielfältigen Möglichkeiten neuer Zähne gar nicht alleine einem Laien nahe bringen.
			      				<p><strong>So ist die Idee von Padento entstanden, dass Menschen sich wie bei einem Optiker über Brillen, sich jetzt auch bei Zahntechnikern über Zahnersatz informieren können.</strong>
							</div>
						</div>
					</section>

					<section class="cta-row section">
						<div class="row">
							<div class="medium-7 columns">
								<div class="padento-box">
									<div class="padento-box-head">
										<p><strong>Video:</strong> Schauen Sie sich einfach unser <strong>Video</strong> an, um in <strong>1 Minute</strong> mehr zu erfahren.</p>
									</div>
									<div class="padento-box-content">
										<div class="flex-video widescreen">
											<iframe src="//fast.wistia.net/embed/iframe/k41j7v41lk?videoFoam=true" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="720" height="405"></iframe><script src="//fast.wistia.net/assets/external/iframe-api-v1.js"></script>
										</div>
									</div>
								</div>
								<!--div class="padento-box">
									<div class="padento-box-head">
										<p>Zahnersatz ist oft eine unvorhergesehene, finanzielle Belastung. <strong>Sie wissen nicht wie Sie Ihren Zahnersatz finanzieren sollen?</strong> Wir bieten Ihnen die Lösung:</p>
									</div>
									<div class="padento-box-content">

									</div>
								</div-->
							</div>
							<div class="medium-5 columns">
								<div class="padento-box">
									<div class="padento-box-head">
										<p>Jetzt <strong class="green">PLZ eingeben</strong> und ein Dentallabor in Ihrer Nähe finden – <strong class="green">kostenlos</strong> und <strong class="green">unverbindlich</strong>!
									</div>
									<div class="padento-box-content">
										<div class="box-wrap">
											@include('common.patient-form', ['formName' => 'form2'])
										</div>
									</div>
								</div>

							</div>
						</div>
					</section>


					<!--section class="process section">
						<div class="row">
							<div class="small-12 columns">
								<h2>Teilzahlung, Beratung vom Zahntechnikermeister, 2. Zahnarztmeinung, Made in Germany</h2>
								<p class="lead">Die <strong class="green">bestmögliche Entscheidungshilfe</strong> für Ihre neuen Zähne:</p>
								<div class="row">
									<div class="medium-6 columns">
										<h3><img src="/img/liste.svg" /> Ablauf</h3>
										<ul>
											<li><span class="blue">01</span> Füllen Sie das <strong>Formular</strong> aus und tragen Sie Ihre Daten dort ein.
											<li><span class="blue">02</span> Sie werden von uns telefonisch kontaktiert, um einen <strong>Termin</strong> zu machen.
											<li><span class="blue">03</span> Sie gehen <strong>zusammen mit dem Zahntechniker zu einem ausgewählten Zahnarzt</strong>, von dem sich auch der Zahntechniker behandeln lassen würde.
											<li><span class="blue">04</span> Ein ausgewählter Zahnarzt ist nicht teurer als andere Zahnärzte. <strong>Er ist einfach nur sehr gut.</strong>
											<li><span class="blue">05</span> Sie bekommen zusätzlich eine <strong>zweite Meinung vom Zahnarzt</strong>, die bei den vielen Möglichkeiten heutzutage absolut wichtig ist.
											<li><span class="blue">06</span> Der Zahntechniker lernt Sie als <strong>Mensch</strong> persönlich kennen und sieht nicht nur das Gips-Modell.
											<li><span class="blue">07</span> Dadurch werden Ihre neuen Zähne <strong>viel besser zu Ihnen passen</strong>.
											<li><span class="blue">05</span> Bei <strong>Angstpatienten</strong> werden spezielle Zahnärzte ausgesucht, die sich damit auskennen.
										</ul>
									</div>
									<div class="medium-6 columns">
										<h3><img src="/img/stern.svg" /> Ihre Vorteile</h3>
										<ul>
											<li><span class="blue">01</span> Der Zahntechniker lernt Sie als Mensch persönlich kennen und sieht nicht nur das Gips-Modell.
											<li><span class="blue">02</span> Sie lernen im Dentallabor mehrere zahntechnische Möglichkeiten kennen
											<li><span class="blue">03</span> Sie können im Dentallabor die Höhe einer Teilzahlung sofort abfragen
											<li><span class="blue">04</span> Sie können beim Zahnarzt Ihre neuen Zähne machen lassen
											<li><span class="blue">Oder:</span> Sie können zu einem Zahnarzt des Dentallabors gehen, um sich eine 2. Zahnarzt-Meinung zu holen
										</ul>
									</div>
								</div>
							</div>
						</div>
					</section-->

					<section class="testimonials section">
						<div class="row">
							<div class="small-12 columns">

								<h2 class="centered">Warum sich Menschen für den Padento-Weg entscheiden</h2>

								<div class="row small-up-1 medium-up-2" data-equalizer data-equalize-on="medium" data-equalize-by-row="true">
									<div class="column">
								    	<div class="padento-box" data-equalizer-watch>
								    		<div class="padento-box-content">
							    				<div class="box-wrap">
									    			<blockquote>
									    				<p>Zu meiner Zahnärztin gehe ich schon seit über 30 Jahren. Sie sagt es wäre bei mir halt so, das alle paar Jahre ein Zahn fällt und die Prothese dann erweitert wird. Damit müsste ich leben. Nur ist das mittlerweile unerträglich. Ich bin froh bei Ihnen im Labor zu sitzen und die tollen Möglichkeiten für Zahnersatz zu sehen und sehr verständlich erklärt zu bekommen. Alle meine Fragen wurden zu meiner vollsten Zufriedenheit beantwortet. Vielen Dank, Herr Broich an Sie und die Zahnarztempfehlung.
											    		<cite>Petra W. aus Köln</cite>
									    			</blockquote>
									    		</div>
											</div>
										</div>
									</div>
									<div class="column">
								    	<div class="padento-box" data-equalizer-watch>
								    		<div class="padento-box-content">
							    				<div class="box-wrap">
									    			<blockquote><p>Ich war schon bei mehreren Zahnärzten und jeder hatte mir etwas anderes empfohlen. Das ist ja auch in anderen Branchen nichts Ungewöhnliches und auch gar nicht schlecht. Jeder hat eben seine eigenen Vorstellungen von den unterschiedlichen Techniken. Aber ich wollte mir selbst mein eigenes Bild machen und so bin ich einfach in eines dieser Dentallabore gegangen, die sich in dieser Aktion für Transparenz bereit erklärt haben. Ich kann das wirklich jedem raten, das zu tun.
								    				<cite>Bernd T. aus Nürnberg</cite>
									    			</blockquote>
									    		</div>
										    </div>
									   	</div>
									</div>
									<div class="column">
								    	<div class="padento-box" data-equalizer-watch>
								    		<div class="padento-box-content">
							    				<div class="box-wrap">
									    			<blockquote><p>Ich habe bei meinem Zahnarzt gar keine Möglichkeiten gezeigt bekommen. Meine Brücke hatte eine silberne Krone, die ich niemals haben wollte. Meine Freundin dagegen wurde von ihrem Zahnarzt sehr gut beraten. Es scheint wirklich Unterschiede in den einzelnen Praxen zu geben. So habe ich mich einfach sicherer gefühlt nach dem Besuch in einem Dentallabor in meiner Nähe.
								    				<cite>Silke F. aus Wuppertal</cite>
									    			</blockquote>
									    		</div>
										    </div>
									   	</div>
									</div>
									<div class="column">
										<div class="padento-box" data-equalizer-watch>
								    		<div class="padento-box-content">
							    				<div class="box-wrap">
									    			<blockquote><p>Der Chef des Dentallabors hat sich echt viel Zeit genommen, mir alle Möglichkeiten der verschiedenen Materialien zu zeigen. Die Vielfalt ist so riesig, da war es für mich schon wichtig über die Vor- und Nachteile genauestens zu informieren.
								    				<cite>Birgit D. aus Hannover</cite>
									    			</blockquote>
									    		</div>
										    </div>
									   	</div>
									</div>
								</div>
							</div>
						</div>
					</section>

					<section class="snd-opinion section">
						<div class="row">
							<div class="small-12 columns">
								<p>Jeder Mensch hat <strong>das Recht</strong>, sich bei neuen Zähnen <strong>voll und ganz zu informieren</strong>.<br/>Die meisten Menschen sind beim Thema Zahnersatz völlig überfordert, weil sie es schwer begreifen können.<br/>Sie können natürlich Ihrem Zahnarzt blind vertrauen.<br/>Es gibt hier aber <strong>große Unterschiede in der Qualität der Beratung</strong>. Viele verlassen die Praxis mit einem Heil-und Kostenplan und haben nicht wirklich verstanden, was sie genau bekommen.</p>
								<p><strong>Nehmen Sie jetzt Ihre Chance wahr</strong> und holen Sie sich <strong>eine professionelle 2. Meinung</strong> beim Zahntechniker und einem auserwählten anderen Zahnarzt, mitreden können und bestens beraten wurden. Mehr geht nicht!..<br/>Es geht um <strong>Ihre Zähne</strong>, um <strong>Ihr Aussehen</strong>, um <strong>Ihre Lebensqualität</strong>, um <strong>Ihre Gesundheit</strong> und um <strong>Ihr Geld</strong>. <br/>Patienten, die den Weg über Padento gegangen sind, hatten danach <strong>das gute Gefühl, eine richtige Entscheidung getroffen zu haben</strong>.</p>
							</div>
						</div>
					</section>
				</div>
			</article>
  		</div>
  	</div>
  </main>

  @if(isset($note))
    <script>
     alert("{{$note}}");
    </script>
  @endif
@stop

@section('foot')
	<script>
		$(document).ready(function() {
			$('.padento-form').submit(function (e) {
				$('.submit-button').attr('disabled','disabled');
			});
		});
	</script>
@endsection