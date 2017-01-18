<div class="object_map" id="google_map"></div>

<script type="text/javascript">
	
	$(document).ready(function(){
				
			var gmap = new GMaps({
				'container_id': 'google_map',
				'map_type': '',
				'latLng': <?=$center ? 'new google.maps.LatLng('.$center['lat'].', '.$center['lng'].')' : '\'Санкт-Петербург\''?>,
				'zoom': 14		
			});
			
			gmap.addMarker({
				'latLng': <?=$center ? 'new google.maps.LatLng('.$center['lat'].', '.$center['lng'].')' : '\'Санкт-Петербург\''?>
			});		
	
	});
				
</script>