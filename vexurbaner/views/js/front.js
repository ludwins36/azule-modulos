
let map;
let directionsDisplay;
function initMapUrbaner() {
	let input = document.getElementById('new_address');
	let input2 = document.getElementById('new_address_modal');

	// var zoomap = vex_glovo_script.zoomap;

	let latlng = new google.maps.LatLng(lat, lnt)

	this.map = new google.maps.Map(document.getElementById('map'), {
		center: latlng,
		zoom: 12
	});

	let destination = new google.maps.Marker({
        draggable: true,
        map: this.map,
        position: latlng,
        label: 'TU',
        title: 'destinatario'

    });

    directionsDisplay = new google.maps.DirectionsRenderer({
        draggable: true,
        map: this.map,
        suppressMarkers: true
    });

    destination.addListener('dragend', (event) => {
        console.log(event);

        // input.value = directionsDisplay.getDirections().routes[0].legs[0].end_address;
        // input2.value = directionsDisplay.getDirections().routes[0].legs[0].end_address;

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