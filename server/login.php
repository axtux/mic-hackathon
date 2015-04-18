<?php
require_once('lib.php');
$_SESSION['ch'] = $ch = md5(time().$_SERVER['REMOTE_ADDR'].'secret!');
echo $ch; //FIXME
?>
<script type="text/javascript" src="js/sha1.js"></script>
<form method="post" action="api.php" onsubmit="dochap();">
	<p><label>E-mail : <input type="email" name="ml" id="e" /></label></p>
	<p><label>Password : <input type="password" name="pw" id="p" /></label></p>
	<p><button type="submit">Login</button></p>
</form>
<script type="text/javascript">
function $i(id) {
	return document.getElementById(id);
}
function dochap() {
	var hp = sha1($i('p').value);
	var ch = '<?=$ch?>';
	$i('p').value = sha1(hp+$i('e').value.toLowerCase()+ch);
	alert($i('p').value ); //FIXME
	return true;
}
</script>
