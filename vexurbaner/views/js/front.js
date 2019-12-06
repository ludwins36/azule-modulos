
let map;
function initMapUrbaner() {
	let input = document.getElementById('new_address');
	let input2 = document.getElementById('new_address_modal');

	// var zoomap = vex_glovo_script.zoomap;

	let latlng = new google.maps.LatLng(lat, lng)

	this.map = new google.maps.Map(document.getElementById('map'), {
		center: latlng,
		zoom: 12
	});

	let destination = new google.maps.Marker({
        draggable: true,
        map: this.map,
        position: end,
        label: 'TU',
        title: 'destinatario'

    });

    let directionsDisplay = new google.maps.DirectionsRenderer({
        draggable: true,
        map: this.map,
        suppressMarkers: true
    });

    destination.addListener('dragend', (event) => {
        let location = directionsDisplay.getDirections();
        location = location.routes[0].legs[0].end_address;
        input.value = location;
        input2.value = location;

    });

	

	
}

jQuery(document).ready(function() {
    if(lat == undefined || lat == ''){
        return;
    }

    if (typeof google === 'undefined') {
        let script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + {$apiGoogle} + '&libraries=places&sensor=false&callback=initMapUrbaner';
        document.head.appendChild(script);

    } else {
        initMapUrbaner();
    }

});