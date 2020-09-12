<p>E-Mail versendet: <strong>{{ $activity->getExtraProperty('subject') }}</strong>.</p>
@if($activity->getExtraProperty('receiver'))
<p>Empfänger: {{ $activity->getExtraProperty('receiver') }}</p>
@else
<p>Empfänger: {{ $activity->subject_type =='App\Lab'? $activity->subject->name :$activity->subject->patientmeta->name  }}</p>
@endif
<p><small>Beschreibung: {{ $activity->getExtraProperty('mail')['description'] }}</small></p>