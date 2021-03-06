<?php
require_once('config.php');

function current_user() {
	return isset($_SESSION['user']) ? $_SESSION['user'] : 0;
}

function create_node($name, $description) {
	global $db;
	$st = $db->prepare('INSERT INTO node(name, description, owner) VALUES(?, ?, ?)');
	$st->execute(array($name, $description, current_user()));
	return $db->lastInsertId();
}

class UploadException extends Exception {
}

/**
 * Uploads a file from a client.
 * @param $name Field name
 * @return id_node
 * @throws UploadException
 */
function do_upload($field, $name, $description) {
	global $db, $UPLOAD_FORBIDDEN;
	$f = $_FILES[$field]; //shortcut
	if (filesize($f['tmp_name']) > UPLOAD_MAX)
		throw new UploadException('File too large.');
	$ext = pathinfo($f['name'], PATHINFO_EXTENSION);
	if (in_array($ext, $UPLOAD_FORBIDDEN))
		throw new UploadException('Forbidden extension.');
	$fname = md5($f['name'].time());
	$pname = get_upload($fname);
	if (@stat($pname))
		throw new UploadException('Duplicate name.');
	if ($f['error'] || !@move_uploaded_file($f['tmp_name'], $pname))
		throw new UploadException('Failed.');
	$id = create_node($name, $description);
	$st = $db->prepare('INSERT INTO file VALUES(?, ?, ?)');
	$st->execute(array($id, $fname, is_previewable($ext)));
	return $id;
}

function is_previewable($ext) {
	return in_array($ext, explode(',', 'pdf,doc,xls,ppt,docx,xlsx,pptx,jpg,png,jpeg,gif,odt,html'));
}

function get_upload($name, $absolute=true) {
	return ($absolute ? $_SERVER['DOCUMENT_ROOT'] : '').UPLOAD_DIR.'/'.$name;
}

function redirect($url) {
	header('Location: '.$url);
	exit();
}

function do_login($email, $hashed_pass, $challenge) {
	global $db;
	if ($_SESSION['ch'] == $challenge) {
		$sql = 'SELECT id_node FROM profile WHERE '.
			'SHA1(CONCAT(password, LOWER(email), ?))=? AND email=?';
		$st = $db->prepare($sql);
		$st->execute(array($challenge, $hashed_pass, $email));
		if ($st->rowCount()) {
			$_SESSION['user'] = $st->fetch()['id_node'];
			redirect('index.php');
		}
	}
	die('login failed');
}

session_start();
