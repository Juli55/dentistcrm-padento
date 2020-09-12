<header>
    <div class="row-fluid">
        <div class="col-md-6">
            <h1 id="title"></h1>
        </div>
        <div class="col-md-6 navbar-right">
            <div class="navbar-field">
              <span data-toggle="tooltip" data-placement="left" title="Profil-Einstellungen in der linken Navigation"
                    aria-hidden="true">

                @if (Auth::user()->hasRole('lab'))
                      Hallo
                      @if(count(Auth::user()->lab)==0)
                          @if(Auth::user()->labs[0] != '' && Auth::user()->labs[0]->labmeta->contact_person != '')
                              <strong>{{ Auth::user()->name }}</strong>
                          @endif
                      @else
                          @if(Auth::user()->lab[0] != '' && Auth::user()->lab[0]->labmeta->contact_person != '')
                              <strong>{{ Auth::user()->lab[0]->labmeta->contact_person }}</strong>
                          @endif
                      @endif
                  @else
                      Hallo <strong>{{ Auth::user()->name }}</strong>
                  @endif
              </span>
            </div>
            @if(session('is_admin') && !Auth::user()->hasRole('admin'))
                <div class="navbar-field">
                    <a href="/fl/{{ session('is_admin') }}"><i class="fa fa-sign-in" aria-hidden="true"></i> Admin</a>
                </div>
            @endif
            <div class="navbar-field">
                <a href="/logout"><i class="fa fa-power-off" aria-hidden="true"></i>
                    Abmelden</a>
            </div>
        <!--div class="navbar-field dropdown">
              <div class="user-panel" data-toggle="dropdown">
                  Einstellungen
                  <span class="caret"></span>
              </div>
              <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
                {{--@if (Auth::user()->hasRole('lab'))--}}
            <li><a href="/labor/{{--{{ Auth::user()->lab[0]->slug }}--}}"><i class="fa fa-globe" aria-hidden="true"> </i>Mein Profil online ansehen</a>
                  <li><a v-link="{ name: 'admin.labSingle', params: {id: {{--{{ Auth::user()->lab->first()->id }}--}} } }"><i class="fa fa-user" aria-hidden="true"></i>
 Mein Laborprofil bearbeiten</a></li>
                  {{--@if (Auth::user()->lab[0]->membership == 1 || Auth::user()->lab[0]->membership == 4)--}}
                <li><a v-link="{ name: 'lab.settings' }"><i class="fa fa-cog"></i> Termin-Einstellungen</a></li>
{{--@endif--}}
                    <li role="separator" class="divider"></li>
{{--@endif--}}

                </ul>
              </div-->
        </div>
    </div>
</header>
