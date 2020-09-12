<p>Kontakt wurde verschoben zu <strong>{{ $activity->getExtraProperty('lab')['name'] }}</strong>.</p>
@if($type = $activity->getExtraProperty('referType'))
    <p>Aktion:
        <strong>
            @if($type == 'notavailable')
                <span>Kontakt ist nicht erreichbar</span>
            @elseif($type == 'selfcontact')
                <span>Labor vereinbart selber einen Termin</span>
            @elseif($type == 'justmove')
                <span>Einfach verschieben</span>
            @endif
        </strong>
    </p>
@endif



