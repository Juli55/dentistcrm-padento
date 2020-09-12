@foreach($input as $key => $value)

	<strong>{{ ucfirst($key) }}:</strong> {{  $value }}<br>

@endforeach