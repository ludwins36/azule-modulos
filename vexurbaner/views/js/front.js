
let directionsDisplay;
function initMapUrbaner() {
	let input = document.getElementById('new_address');
	let input2 = document.getElementById('new_address_modal');

	// var zoomap = vex_glovo_script.zoomap;

    let end = new google.maps.LatLng(lat, lnt)
    let a = new google.maps.places.Autocomplete(input);
    let b = new google.maps.places.Autocomplete(input2);
    let latlng = new google.maps.LatLng(parseFloat(latS), parseFloat(lntS));

	let map = new google.maps.Map(document.getElementById('map'), {
		center: latlng,
		zoom: 12
    });
    let directionsService = new google.maps.DirectionsService();
    
    let img = {
        url: image,
        size: new google.maps.Size(80, 100),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(60, 60)
	};

	new google.maps.Marker({
		position: latlng,
		map: map,
		icon: img,
		title: 'Tienda'
	});


	let destination = new google.maps.Marker({
        draggable: true,
        map: map,
        position: end,
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
        input.value = location;
        input2.value = location;

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
    var l = document.getElementById('lnk-azule-kids');
    var r = document.getElementById('category-11');
    console.log(l);
    if(typeof l === 'undefined' || l == null){
        return;
    }

    l.children[0].innerHTML = '<label><span style="color: blue;"><b>AZULE KIDS</b></span></label>';
    r.children[0].innerHTML = '<label><span style="color: red;"><b>REGALOS</b></span></label>';

    if(typeof lat === 'undefined'){
        return;
    }
    

    if (typeof google === 'undefined') {
        let script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiGoogle + '&libraries=places&sensor=false&callback=initMapUrbaner';
        document.head.appendChild(script);

    } else {
        initMapUrbaner();
    }

    

});