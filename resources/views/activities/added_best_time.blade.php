<table class="table">
    <tr>
        <td><strong>Wunschzeiten</strong></td>
        <td><strong>Von</strong></td>
        <td><strong>Bis</strong></td>
    </tr>
    <tr>
        <td><strong>Werktags</strong></td>
        <td>{{ $activity->getExtraProperty('workday_from') }}</td>
        <td>{{ $activity->getExtraProperty('workday_till') }}</td>
    </tr>
    <tr>
        <td><strong>Samstags</strong></td>
        <td>{{ $activity->getExtraProperty('weekend_from') }}</td>
        <td>{{ $activity->getExtraProperty('weekend_till') }}</td>
    </tr>
</table>