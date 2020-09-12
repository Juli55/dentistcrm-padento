<p>Phasen-Termin hinzugefügt für den
    <strong>{{ \Carbon\Carbon::parse($activity->getExtraProperty('date'))->format('d.m.Y H:i') }}</strong>

    @if($activity->getExtraProperty('phase'))
        ({{ contact_phases($activity->getExtraProperty('phase')) }})
    @endif
    .</p>