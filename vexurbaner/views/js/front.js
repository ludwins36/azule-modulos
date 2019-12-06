
let map;
function initMapGlovo() {
	verificGlovo();
	latInit = vex_glovo_script.latInit;
	lngInit = vex_glovo_script.longInit;
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