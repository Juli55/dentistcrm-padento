@if($activities->count())

    <section id="notes">
        @foreach ($activities as $activity)
            @if($activity->visibility)
            <article class="note">
                <i class="icon"><i class="fa fa-comment" aria-hidden="true"></i></i>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <p>
                                <strong>{{ $activity->causer_name }}</strong>
                                <br>
                                @if($activity->created_at->lt(\Carbon\Carbon::now()->subHour()))
                                    <small>{{ $activity->created_at->format('d.m.Y H:i') }} Uhr</small>
                                @else
                                    <small>{{ $activity->created_at->diffForHumans() }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-9 note-content">
                            @if(is_string($activity->description))
                                @if (view()->exists("activities.{$activity->description}"))
                                    @include ("activities.{$activity->description}")
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </article>
            @endif
        @endforeach
    </section>

@endif