<p>Kontakt befindet sich in der Bearbeitung von Padento.</p>
@if($type = $activity->getExtraProperty('type'))
    <p>Aktion:
        @if($type == 'nodate')
            <span>Kontakt von Padento bearbeiten lassen</span>
        @elseif($type == 'canceled')
            <span>Kontakt hat den Termin abgesagt und benötigt einen neuen</span>
        @endif
    </p>
@endif