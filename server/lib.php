<?php
require_once('config.php');

function current_user() {
	//FIXME
	return 4;
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

function display_upform($name) {
?><form method="post" enctype="multipart/form-data" action="?">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?=UPLOAD_MAX?>" />
	<input type="file" name="<?=$name?>" />
	<input type="submit" name="upload" value="Envoyer" />
</form>
<?php
}

session_start();
