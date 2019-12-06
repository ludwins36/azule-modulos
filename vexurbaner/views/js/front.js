
let directionsDisplay;
function initMapUrbaner() {
	let input = document.getElementById('new_address');
	let input2 = document.getElementById('new_address_modal');

	// var zoomap = vex_glovo_script.zoomap;

	let end = new google.maps.LatLng(lat, lnt)
    console.log(latS);
    console.log(lntS);
    let latlng = new google.maps.LatLng(parseFloat(latS), parseFloat(lntS));

	let map = new google.maps.Map(document.getElementById('map'), {
		center: latlng,
		zoom: 12
    });
    let directionsService = new google.maps.DirectionsService();
    let img = {
		url: image
	};

	new google.maps.Marker({
		position: latlng,
		map: this.map,
		icon: img,
		title: 'Tienda'
	});


	let destination = new google.maps.Marker({
        draggable: true,
        map: map,
        position: latlng,
        label: 'TU',
        title: 'destinatario'

    });

    let directionsDisplay = new google.maps.DirectionsRenderer({
        draggable: true,
        map: map,
        suppressMarkers: true
    });

    destination.addListener('dragend', (event) => {
        displayRoute(latlng, event.latLng, directionsService, directionsDisplay);
        let location = directionsDisplay.getDirections();
        location = location.routes[0].legs[0].end_address;
        input.value = location.routes[0].legs[0].end_address;
        input2.value = location.routes[0].legs[0].end_address;

    });

	
    displayRoute(latlng, end, directionsService, directionsDisplay);
	
}

function displayRoute(origin, destination, service, display) {
	service.route({
		origin: origin,
		destination: destination,
		travelMode: google.maps.DirectionsTravelMode.DRIVING,
		unitSystem: google.maps.UnitSystem.METRIC
	}, function (response, status) {
		if (status === 'OK') {
			display.setDirections(response);

		}
	});
}

jQuery(document).ready(function() {
    console.log(typeof lat);
    if(typeof lat === 'undefined'){
        return;
    }
    console.log(apiGoogle);

    if (typeof google === 'undefined') {
        let script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiGoogle + '&libraries=places&sensor=false&callback=initMapUrbaner';
        document.head.appendChild(script);

    } else {
        initMapUrbaner();
    }

});