<section id="content">
    <aside style="z-index:inherit;">
        <header>
            <a v-link="{ name: 'home' }"><img class="logo" src="/images/Padento_Claim_RGB_weiss.png"></a>
        </header>
        <nav class="main-sidebar-nav">
            <div class="nav-wrap">

                @role('admin')
                <h4>Navigation</h4>
                <ul class="nav">
                    <li><a v-link="{ name: 'home' }"><i class="fa fa-tachometer"></i> Übersicht</a></li>
                    <li><a v-link="{ name: 'admin.contacts', params: {type: 'all'} }"><i class="fa fa-user"></i>
                            Kontakte</a></li>
                    <li><a v-link="{ name: 'admin.dentistsContacts' }"><i class="fa fa-user-md"></i> Zahnärzte</a></li>
                    <li><a v-link="{ name: 'admin.labs' }"><i class="fa fa-user-md"></i> Labore</a></li>
                    <li><a v-link="{ name: 'my.calendar' }"><i class="fa fa-calendar"></i> Rückruftermine</a></li>

                </ul>
                <hr>

                <h4>Administration</h4>
                <ul class="nav">
                    <!-- li><a v-link="{ name: 'stats' }"><i class="fa fa-bar-chart"></i> Statistiken</a></li -->
                    <li><a v-link="{ name: 'dentiststats' }"><i class="fa fa-bar-chart"></i> Zahnarzt Statistiken</a></li>
                    {{--<li><a v-link="{ name: 'my.dentistsCalendar' }"><i class="fa fa-calendar"></i> Zahnärzte Termine--}}
                    {{--<span class="label label-success">NEU</span></a></li>--}}

                    <li><a v-link="{ name: 'admin.settings.users' }"><i class="fa fa-users"></i> Nutzermanagement</a>
                    </li>
                    <li><a v-link="{ name: 'admin.settings.mails' }"><i class="fa fa-envelope"></i> E-Mails</a></li>
                    <li><a v-link="{ name: 'admin.settings.index' }"><i class="fa fa-cog"></i> Einstellungen</a></li>
                    <li><a href="/app/countries"><i class="fa fa-plus"></i> Postleitzahlen</a>
                    <li><a href="/app/todo"><i class="fa fa-wrench"></i> ToDo Verwaltung</a></li>
                    <li><a href="/app/links"><i class="fa fa-link"></i> Links</a></li>
                </ul>
                <hr>

                <h4>Sonstiges</h4>
                <ul class="nav">
                    <li><a href="https://docs.google.com/document/d/15ecuSntYzdBYpVt6pVrwcCKIIKBxAs7E6PEpTFJV2HY/edit"
                           target="_blank"><i class="fa fa-book" aria-hidden="true"></i> Dokumentation</a></li>
                    <li><a v-link="{ name: 'tools' }"><i class="fa fa-wrench" aria-hidden="true"></i> Tools</a></li>
                    <li><a href="/dashboard/karte" target="_blank"><i class="fa fa-map-marker"></i> Padento-Karte</a>
                    </li>
                </ul>
                <hr>
                @endrole


                @role('user')
                <h4>Navigation</h4>
                <ul class="nav">
                    <li><a v-link="{ name: 'home' }"><i class="fa fa-tachometer"></i> Übersicht</a></li>
                    <li><a v-link="{ name: 'admin.contacts', params: {type: 'all'} }"><i class="fa fa-user"></i>
                            Kontakte</a></li>
                    <li><a v-link="{ name: 'admin.labs' }"><i class="fa fa-user-md"></i> Labore</a></li>
                    <li><a v-link="{ name: 'my.calendar' }"><i class="fa fa-calendar"></i> Rückruftermine</a></li>
                </ul>
                <hr>
                @endrole

                @role('lab')
                @if(count(Auth::user()->lab) == 0)
                    @if ( count(Auth::user()->labs) !== 0 )
                        @if (Auth::user()->labs[0]->membership == 0 || Auth::user()->labs[0]->membership == 1 || Auth::user()->labs[0]->membership == 2 || Auth::user()->labs[0]->membership == 3 || Auth::user()->labs[0]->membership == 4)
                            <h4>Navigation</h4>
                            <ul class="nav">
                                <li><a v-link="{ name: 'home' }"><i class="fa fa-tachometer"></i> Übersicht</a></li>
                                <li><a v-link="{ name: 'admin.contacts', params: {type: 'all'} }"><i class="fa fa-user"></i>
                                        Kontakte</a></li>
                                <li><a v-link="{ name: 'my.calendar' }"><i class="fa fa-calendar"></i> Termine</a></li>
                                {{--<li v-if="whoami.lab[0].has_multi_user && whoami.lab[0].user_id === whoami.id">--}}
                            </ul>
                            <hr>
                        @endif
                    @endif

                @else

                    @if(Auth::user()->lab[0]->membership == 0 || Auth::user()->lab[0]->membership == 1 || Auth::user()->lab[0]->membership == 2 || Auth::user()->lab[0]->membership == 3 || Auth::user()->lab[0]->membership == 4)
                        <h4>Navigation</h4>
                        <ul class="nav">
                            <li><a v-link="{ name: 'home' }"><i class="fa fa-tachometer"></i> Übersicht</a></li>
                            <li><a v-link="{ name: 'admin.contacts', params: {type: 'all'} }"><i class="fa fa-user"></i>
                                    Kontakte</a></li>
                            <li><a v-link="{ name: 'my.calendar' }"><i class="fa fa-calendar"></i> Termine</a></li>
                            {{--<li v-if="whoami.lab[0].has_multi_user && whoami.lab[0].user_id === whoami.id">--}}
                        </ul>
                        <hr>
                    @endif
                @endif
                @endrole


                @role('lab')
                @if(count(Auth::user()->lab) == 0)
                    @if (!Auth::user()->hasRole('dentist-crm-user') && !Auth::user()->labs[0]->membership == 5)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a href="http://www.rainerehrich.de/crm-hinter-den-kulissen" target="_blank"><i
                                            class="fa fa-user-md"></i> Upgrade Zahnärzte</a>
                            </li>
                        </ul>
                    @endif
                @else
                    @if (!Auth::user()->hasRole('dentist-crm-user') && !Auth::user()->lab[0]->membership == 5)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a href="http://www.rainerehrich.de/crm-hinter-den-kulissen" target="_blank"><i
                                            class="fa fa-user-md"></i> Upgrade Zahnärzte</a>
                            </li>
                        </ul>
                    @endif
                @endif
                @endrole

                @role('dentist-crm-user')
                @if(count(Auth::user()->lab)==0)
                    @if (Auth::user()->labs[0]->membership == 0 || Auth::user()->labs[0]->membership == 1)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a v-link="{ name: 'admin.dentistsContacts' }"><i class="fa fa-user-md"></i> Zahnärzte
                                   </a></li>
                            <li><a v-link="{ name: 'dentiststats' }"><i class="fa fa-bar-chart"></i> Statistiken </a></li>
                            <li><a v-link="{ name: 'my.dentistsCalendar' }"><i class="fa fa-calendar"></i> Termine </a></li>
                            <li v-if="whoami.lab[0].user_id === whoami.id">
                                <a v-link="{ name: 'lab.users' }"><i class="fa fa-user"></i> Laborbenutzer
                                   </a>
                            </li>
                        </ul>
                        <hr>
                    @endif
                @else
                    @if (Auth::user()->lab[0]->membership == 0 || Auth::user()->lab[0]->membership == 1)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a v-link="{ name: 'admin.dentistsContacts' }"><i class="fa fa-user-md"></i> Zahnärzte
                                   </a></li>
                            <li><a v-link="{ name: 'dentiststats' }"><i class="fa fa-bar-chart"></i> Statistiken </a></li>
                            <li><a v-link="{ name: 'my.dentistsCalendar' }"><i class="fa fa-calendar"></i> Termine </a></li>
                            <li v-if="whoami.lab[0].user_id === whoami.id">
                                <a v-link="{ name: 'lab.users' }"><i class="fa fa-user"></i> Laborbenutzer
                                   </a>
                            </li>
                        </ul>
                        <hr>
                    @endif
                @endif
                @endrole


                @role('lab')
                @if(count(Auth::user()->lab)==0)
                    @if (Auth::user()->labs[0]->membership == 5)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a v-link="{ name: 'admin.dentistsContacts' }"><i class="fa fa-user-md"></i> Zahnärzte
                                   </a></li>
                            <li><a v-link="{ name: 'dentiststats' }"><i class="fa fa-bar-chart"></i> Zahnarzt
                                    Statistiken</a></li>
                            <li><a v-link="{ name: 'my.dentistsCalendar' }"><i class="fa fa-calendar"></i> Zahnärzte
                                    Termine</a></li>
                            <li><a v-link="{ name: 'lab.settings' }"><i class="fa fa-cog"></i> Termin-Einstellungen</a>
                            </li>
                        </ul>
                        <hr>
                        <h4>Mein Profil</h4>
                        <ul class="nav">
                            <li><a href="/labor/{{ Auth::user()->labs[0]->slug }}" target="_blank"><i
                                            class="fa fa-globe" aria-hidden="true"> </i>Mein Profil online ansehen</a>
                            <li>
                                <a v-link="{ name: 'admin.labSingle', params: { id: {{ Auth::user()->labs->first()->id }} } }"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>Mein Profil bearbeiten</a>
                            </li>
                        </ul>
                    @endif
                @else
                    @if (Auth::user()->lab[0]->membership == 5)
                        <h4>Zahnärzte</h4>
                        <ul class="nav">
                            <li><a v-link="{ name: 'admin.dentistsContacts' }"><i class="fa fa-user-md"></i> Zahnärzte
                                   </a></li>
                            <li><a v-link="{ name: 'dentiststats' }"><i class="fa fa-bar-chart"></i> Zahnarzt
                                    Statistiken</a></li>
                            <li><a v-link="{ name: 'my.dentistsCalendar' }"><i class="fa fa-calendar"></i> Zahnärzte
                                    Termine</a></li>
                            <li v-if="whoami.lab[0].user_id === whoami.id">
                                <a v-link="{ name: 'lab.users' }"><i class="fa fa-user"></i> Laborbenutzer
                                   </a>
                            </li>
                        </ul>
                        <hr>
                        <h4>Mein Profil</h4>
                        <ul class="nav">
                            <li><a href="/labor/{{ Auth::user()->lab[0]->slug }}" target="_blank"><i class="fa fa-globe"
                                                                                                     aria-hidden="true"> </i>Mein
                            Profil online ansehen</a>
                            <li>
                                <a v-link="{ name: 'admin.labSingle', params: { id: {{ Auth::user()->lab->first()->id }} } }"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>Mein Profil bearbeiten</a>
                            </li>
                            <li><a v-link="{ name: 'lab.settings' }"><i class="fa fa-cog"></i> Termin-Einstellungen</a>
                            </li>
                        </ul>
                    @endif
                @endif
                @endrole


                @role('lab')
                @if(count(Auth::user()->lab)==0)
                    @if (Auth::user()->labs[0]->membership == 0 || Auth::user()->labs[0]->membership == 1 || Auth::user()->labs[0]->membership == 2 || Auth::user()->labs[0]->membership == 3 || Auth::user()->labs[0]->membership == 4)
                        <h4>Mein Profil</h4>
                        <ul class="nav">
                            <li><a href="/labor/{{ Auth::user()->labs[0]->slug }}" target="_blank"><i
                                            class="fa fa-globe"
                                            aria-hidden="true"> </i>Mein
                                    Profil online ansehen</a>
                            <li>
                                <a v-link="{ name: 'admin.labSingle', params: { id: {{ Auth::user()->labs->first()->id }} } }"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    Mein Profil bearbeiten</a></li>

                            @if (Auth::user()->labs[0]->membership == 1 || Auth::user()->labs[0]->membership == 4  || Auth::user()->labs[0]->membership == 5 ||Auth::user()->labs[0]->membership == 0 )
                                <li><a v-link="{ name: 'lab.settings' }"><i class="fa fa-cog"></i> Termin-Einstellungen</a>
                                </li>
                                <li><a v-link="{ name: 'stats' }"><i class="fa fa-bar-chart"></i> Statistiken</a></li>
                            @endif
                        </ul>
                        <hr>
                    @endif
                @else
                    @if (Auth::user()->lab[0]->membership == 0 || Auth::user()->lab[0]->membership == 1 || Auth::user()->lab[0]->membership == 2 || Auth::user()->lab[0]->membership == 3 || Auth::user()->lab[0]->membership == 4)
                        <h4>Mein Profil</h4>
                        <ul class="nav">
                            <li><a href="/labor/{{ Auth::user()->lab[0]->slug }}" target="_blank"><i class="fa fa-globe"
                                                                                                     aria-hidden="true"> </i>Mein
                                    Profil online ansehen</a>
                            <li>
                                <a v-link="{ name: 'admin.labSingle', params: { id: {{ Auth::user()->lab->first()->id }} } }"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    Mein Profil bearbeiten</a></li>

                            @if (Auth::user()->lab[0]->membership == 1 || Auth::user()->lab[0]->membership == 4  || Auth::user()->lab[0]->membership == 5 ||Auth::user()->lab[0]->membership == 0 )
                                <li><a v-link="{ name: 'lab.settings' }"><i class="fa fa-cog"></i> Termin-Einstellungen</a>
                                </li>
                                <li><a v-link="{ name: 'stats' }"><i class="fa fa-bar-chart"></i> Statistiken</a></li>
                            @endif
                        </ul>
                        <hr>
                    @endif
                @endif

                @endrole
                @if(count(Auth::user()->lab))
                @if(Auth::user()->lab[0]->membership != 5)
                    <h4>Hilfreiches</h4>
                    <ul class="nav">
                        <li class="guide">
                            <a v-link="{ name: 'guides' }">
                                <i class="fa fa-book"></i> Padento Guide
                                <ul>
                                    <li>
                                        <small>
                                            Technische Einführung - Login Bereich
                                        </small>
                                    </li>
                                    <li>
                                        <small>
                                            Wie Sie mit Padento erfolgreich Patienten gewinnen
                                        </small>
                                    </li>
                                </ul>
                            </a>
                        </li>
                        <li><a href="{{ url('downloads/padento_checkliste_gespraeche') }}"><i class="fa fa-book"></i>Checkliste
                                für Gespräche</a></li>
                        <li><a href="{{ url('downloads/padento_termine_machen') }}"><i class="fa fa-book"></i> Termine
                                machen</a></li>
                        <li><a href="{{ url('downloads/padento_wie_funktioniert_der_telefonservice') }}"><i
                                        class="fa fa-book"></i> Wie funktioniert der Telefonservice</a></li>
                        <li><a href="{{ url('downloads/mail_vorlagen') }}"><i class="fa fa-book"></i> Mailvorlagen</a></li>

                    </ul>
                @endif
                @endif
                <hr>
                <h4>Marketing</h4>
                <ul class="nav">
                    <li><a v-link="{ name: 'downloads' }"><i class="fa fa-rocket"></i> Downloads</a></li>
                    <li><a v-link="{ name: 'partner' }"><i class="fa fa-building"></i> Partner</a></li>
                </ul>


                <!--li><a v-link="{ name: 'admin.contacts', params: {test:'test'} }"><i class="fa fa-user"></i> Kontakte <small>(Schleife)</small></a></li-->
                <!-- <li><a v-link="{ name: 'admin.dentists' }"><i class="fa fa-user-md"></i> Zahnärzte</a></li> -->
                {{--<li><a href="#"><i class="fa fa-facebook"></i> Facebook-Pixel-Reset</a></li>--}}
                {{--<li><a v-link="{ name: 'docs' }"><i class="fa fa-book" aria-hidden="true"></i> Dokumentation</a></li>--}}
                {{--<li><a v-link="{ name: 'admin.settings.properties' }"><i class="fa fa-list"></i> Kontakteigenschaften</a></li>--}}


            </div>
        </nav>
    </aside>
    <main>
        <div>
            @include('common.nav')
            <router-view></router-view>
        </div>
    </main>
</section>
