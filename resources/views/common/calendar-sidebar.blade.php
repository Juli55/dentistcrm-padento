<section id="content">
    <aside>
        <header>
            <a v-link="{ name: 'home' }"><img class="logo" src="/images/Padento_Claim_RGB_weiss.png"></a>
        </header>
        <nav class="main-sidebar-nav">
            <div class="nav-wrap">
                <h4>Navigation</h4>
                <ul class="nav">
                    @role(['admin', 'user'])
                    <li><a href="/app/labore"><i class="fa fa-user-md"></i> Labore</a></li>
                    @endrole
                    @role('lab')
                    <li><a href="/calendar"><i class="fa fa-calendar" aria-hidden="true"></i> Termine</a></li>
                    @endrole
                    @role('admin')
                    <li><a v-link="{ name: 'admin.dentists' }"><i class="fa fa-user-md"></i> Zahnärzte</a></li>
                    @endrole
                    <li><a href="/app/kontakte"><i class="fa fa-user"></i> Kontakte</a></li>
                </ul>
                <hr>
                @role('admin')
                <h4>Administration</h4>
                <ul class="nav">
                    <li><a v-link="{ name: 'admin.settings.properties' }"><i class="fa fa-list"></i>
                            Kontakteigenschaften</a></li>
                    <li><a v-link="{ name: 'admin.settings.index' }"><i class="fa fa-cog"></i> Einstellungen</a></li>
                    <li><a v-link="{ name: 'admin.settings.users' }"><i class="fa fa-users"></i> Nutzermanagement</a>
                    </li>
                    <li><a v-link="{ name: 'admin.logs' }"><i class="fa fa-cogs"></i> Logs</a></li>
                    <li><a v-link="{ name: 'admin.settings.mails' }"><i class="fa fa-envelope"></i> E-Mails</a></li>
                </ul>
                <hr>
                @endrole
                <h4>Sonstiges</h4>
                <ul class="nav">
                    @role('admin')
                    <li><a href="#"><i class="fa fa-facebook"></i> Facebook-Pixel-Reset</a></li>
                    @endrole
                    <li><a href="/dashboard/karte" target="_blank"><i class="fa fa-map-marker"></i> Padento-Karte</a>
                    </li>
                </ul>
                <hr>
                <h4>Hilfreiches</h4>
                <ul class="nav">
                    <li><a href="#"><i class="fa fa-book"></i> Padento Guide</a></li>
                </ul>
                <hr>
                <h4>Marketing</h4>
                <ul class="nav">
                    <li><a href="#"><i class="fa fa-rocket"></i> Marketing</a></li>
                    <li><a href="#"><i class="fa fa-building"></i> Partner</a></li>
                </ul>
            </div>
        </nav>
    </aside>
    <main>
        <div>
            @include('common.nav')
            <div id="calendar"></div>
        </div>
    </main>
</section>
