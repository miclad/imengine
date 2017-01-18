function GMaps(params){
	
	this.markersArray = new Array();
	this.infoWindowsArray = new Array();
	this.path = null;
	this.marker_counter = 0;
	
	var myOptions = {
		disableDefaultUI: params.disable_ui,
		zoom: params.zoom ? params.zoom : 3,
		mapTypeId: params.map_type ? params.map_type : google.maps.MapTypeId.ROADMAP,
		navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
		scrollwheel: true//params.scrollwheel ? (params.scrollwheel == 2 ? true : false) : false
	};
    	
	this.map = new google.maps.Map(document.getElementById(params.container_id), myOptions);    
	
	if(params.latLng){
		if(typeof params.latLng == 'string'){
			this.findAddress(params.latLng, params.callback, false);
		}else{
			this.map.setCenter(params.latLng);
		}
	}else{
		this.setDefaultCenter();
		//this.getUserLocation();
	}
	
}

GMaps.prototype = {
	
	addMarker: function(params){
		
		var oThis = this;
		
		var iw = -1;
		var marker = new google.maps.Marker({
			'position': params.latLng,
			'map': this.map,
			'title': params.title ? params.title : '',
			'icon': params.icon ? params.icon : '',
			'draggable': params.draggable
		});
		
		if(params.text){
			if(!params.open_by_click)
				var iw = this.addInfoWindow(params.text, null, marker);	
			else{						
				var iw = this.addInfoWindow(params.text);
				google.maps.event.addListener(
					marker,
					'click',
					function(){
						oThis.infoWindowsArray[iw].open(oThis.map, marker);
					}
				);
			}
		}
		
		var lngth = oThis.markersArray.length;
		
		if(params.onclick_event){
			google.maps.event.addListener(
				marker,
				'click',
				function(){
					params.onclick_event(oThis, marker);					
				}
			);
		}
		
		if(params.ondragstart_event){
			google.maps.event.addListener(
				marker,
				'dragstart',
				function(){
					params.ondragstart_event(oThis, marker);					
				}
			);
		}
		
		if(params.ondragend_event){
			google.maps.event.addListener(
				marker,
				'dragend',
				function(){
					params.ondragend_event(oThis, marker);					
				}
			);
		}
		
		this.markersArray.push({'marker': marker, 'iw': iw, 'unique_id': ++this.marker_counter, 'descr': params.descr ? params.descr : false});
		
	},
	
	removeMarkers: function(i){
		for(var i=0; i<this.markersArray.length; i++){
			this.removeMarker(i, false);
		}
		this.markersArray.length = 0;
	},
	
	removeMarker: function(ind, fix_array){
		
		if(ind < this.markersArray.length){
			this.markersArray[ind]['marker'].setMap(null);
			if(this.markersArray[ind]['iw'] > -1)
				this.infoWindowsArray[this.markersArray[ind]['iw']].close();
			if(fix_array)
				this.markersArray[ind] = false;
		}
		
		if(fix_array){
			var t_arr = new Array();
			for(var i=0; i<this.markersArray.length; i++){
				if(this.markersArray[i])
					t_arr[t_arr.length] = this.markersArray[i];
			}
			this.markersArray = t_arr;
			this.markersArray.length = t_arr.length;
		}
		
	},
	
	addInfoWindow: function(text, latLng, marker, autopan){
		
		var wnd = new google.maps.InfoWindow({disableAutoPan: autopan});
		wnd.setContent(text);
		if(latLng){
			wnd.setPosition(latLng);
			wnd.open(this.map);
		}else{
			if(marker){
				wnd.open(this.map, marker);
			}
		}
		
		this.infoWindowsArray.push(wnd);
		return this.infoWindowsArray.length-1;
		
	},
	
	findAddress: function(adr, callback, add_marker){				
		var oThis = this;		
		geocoder = new google.maps.Geocoder();	
		geocoder.geocode(
			{'address': adr},
			function(results, status){
				if(status == google.maps.GeocoderStatus.OK){
					oThis.map.setCenter(results[0].geometry.location);
					if(add_marker)
						oThis.addMarker(results[0].geometry.location);
					if(callback)
						callback(oThis);
				}else{
					alert("Geocode was not successful for the following reason: " + status);
				}
			}
		);	
	},
	
	setEventHandler: function(ev, callback){
		var oThis = this;
		google.maps.event.addListener(
			oThis.map, 
			ev, 
			function(event){
				if(event)
					callback(event, oThis);
				else
					callback(oThis);
			}
		);
	},
	
	getUserLocation: function(){			
		var oThis = this;			
		if(navigator.geolocation){
			browserSupportFlag = true;
			navigator.geolocation.getCurrentPosition(
				function(position) {
					oThis.map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));					
				}, 
				function(){
					oThis.setDefaultCenter();
				}
			);		
		}else{		
			// Try Google Gears Geolocation
			if(google.gears){ 
				var geo = google.gears.factory.create('beta.geolocation');
				geo.getCurrentPosition(
					function(position) {
						oThis.map.setCenter(new google.maps.LatLng(position.latitude,position.longitude));
					},
					function(){
						oThis.setDefaultCenter();
					}
				);
			}
		}
	},
	
	setDefaultCenter: function(){
		var LatLng = new google.maps.LatLng(55.74412,37.617188);
		this.map.setCenter(LatLng);
	},
	
	getCenter: function(){
		return this.map.getCenter();
	},
	
	buildPath: function(){
		
		if(this.path)
			this.path.setMap(null);
		
		var coordinates = new Array();
		
		for(var i=0; i<this.markersArray.length; i++){
			coordinates[coordinates.length] = this.markersArray[i].marker.position;
		}	
		
		this.path = new google.maps.Polyline({
			path: coordinates,
			strokeColor: "#FF0000",
			strokeOpacity: 0.8,
			strokeWeight: 2
		});
		
		this.path.setMap(this.map);

		
	}
	
}