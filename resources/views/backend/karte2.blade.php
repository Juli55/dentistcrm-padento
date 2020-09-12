@extends('layouts.default')

@section('head')
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
{{--<script src="/js/map.js"></script>--}}
<script src="https://openlayers.org/api/OpenLayers.js"></script>
@stop

@section('content')
<div class="container admin-panel">
	<div class="row">
		<div class="large-12 columns">

			<header>
				<hr>
				<h1>Alle Padento-Labore</h1>
				<hr>
			</header>

			<div id="Map" style="height:800px;margin:2em;"></div>
			<script>
			var lat            = {{$geolat}};//47.35387;
			var lon            = {{$geolong}};//8.43609;
			var zoom           = 6;
			var labs = [
				@foreach($glabs as $glab)
					[ {{$glab->geolat}}, {{$glab->geolon}} ],
				@endforeach
					[ {{$geolat}}, {{$geolong}} ]
				];

			var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
			var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
			var position       = new OpenLayers.LonLat(lon, lat).transform( fromProjection, toProjection);
			map = new OpenLayers.Map("Map");
			var mapnik         = new OpenLayers.Layer.OSM();
			map.addLayer(mapnik);

			var markers = new OpenLayers.Layer.Markers( "Markers" );
			map.addLayer(markers);
			@foreach($glabs as $glab)
				markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat({{$glab->lat}},{{$glab->lon}}).transform( fromProjection, toProjection)));
			@endforeach
			map.setCenter(position, zoom);
			</script>
	<hr style="margin:2rem;">
		</div>
	</div>
</div>
@endsection
