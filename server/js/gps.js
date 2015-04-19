if (navigator.geolocation)
	$('#getGPS').show();

function getLocation() {
	navigator.geolocation.getCurrentPosition(showPosition);
}

function showPosition(position) {
	$('#lat').value = position.coords.latitude;
	$('#lon').value = position.coords.longitude;
}
