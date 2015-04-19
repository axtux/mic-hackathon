function $i(id) {
	return document.getElementById(id);
}
function dochap(ch) {
	var hp = sha1($i('p').value);
	//alert((hp+$i('e').value.toLowerCase()+ch));
	$i('p').value = sha1(hp+$i('e').value.toLowerCase()+ch);
	return true;
}
