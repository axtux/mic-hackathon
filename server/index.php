<?php
require_once('config.php');
$fields = array(
	'data' => array('name', 'description'),
	'profile' => array('email'),
	'gps' => array('latitude', 'longitude'),
	'url' => array('link'),
	'file');
function current_user() {
	//FIXME
	return 4;
}
switch ($_SERVER['REQUEST_METHOD']) {
case 'GET':
	$id_node = (int)$_GET['id_node'];
	$sql = 'SELECT y.* FROM ('.
		'SELECT NULL AS alpha, NULL AS beta, NULL AS relation, x.* '.
		'FROM bignode x WHERE id_node=? UNION ALL '.
		'SELECT * FROM edge INNER JOIN bignode x ON '.
		'alpha=x.id_node WHERE beta=? UNION ALL '.
		'SELECT * FROM edge INNER JOIN bignode x ON '.
		'beta=x.id_node WHERE alpha=?'.
		') y INNER JOIN profile u ON ?=u.id_node ';
		//'WHERE y.is_deleted=u.is_omniscient';
	$st = $db->prepare($sql);
	$st->execute(array($id_node, $id_node, $id_node, current_user()));
	echo json_encode($st->fetchAll());
	break;
case 'POST':
	if (isset($_POST['id_node'])) {
	}
	break;
case 'DELETE':
	//TODO array
	//break;
}

/*foreach ($db->query('SELECT * FROM node') as $r)
	echo json_encode($r);*/
