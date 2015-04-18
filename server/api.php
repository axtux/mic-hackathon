<?php
require_once('lib.php');
$fields = array(
	'node' => array('name', 'description'),
	'profile' => array('email'),
	'gps' => array('latitude', 'longitude'),
	'url' => array('link'),
	'file'); //test this

function fromPost($k) {
	return $_POST[$k];
}

function doUpdate() {
	global $db;
	$st = $db->prepare('INSERT INTO edge VALUES(?, '.doInsert().', \'improvement\')');
	$st->execute(array($_POST['id_node']));
}

function doInsert() {
	global $db, $fields;
	$st = $db->prepare('INSERT INTO node(name, description, owner) VALUES(?, ?, ?)');
	$st->execute(array($_POST['name'], $_POST['description'], current_user()));
	$id = $db->lastInsertId();
	foreach ($fields as $k=>$v) {
		if (in_array($v[0], array_keys($_POST)) && $k!='node') {
			$sql = "INSERT INTO $k(".join(', ', array('id_node')+$v).') VALUES(';
			$sql .= join(', ', array_fill(0, sizeof($v), '?')).')';
			$st = $db->prepare($sql);
			$st->execute(array($id)+array_map('fromPost', $v));
			return $db->lastInsertId();
		}
	}
	return $id;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); //FIXME
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
	if (isset($_POST['relation'])) {
		$sql = 'UPDATE edge SET relation=? WHERE alpha=? AND beta=?';
		$st = $db->prepare($sql);
		$st->execute(array($_POST['relation'], $_POST['alpha'], $_POST['beta']));
		if (! $st->rowCount()) {
			$sql = 'INSERT INTO edge VALUES(?, ?, ?)';
			$st = $db->prepare($sql);
			$st->execute(array($_POST['alpha'], $_POST['beta'], $_POST['relation']));
		}
	}
	else {
		$db->beginTransaction();
		try {
			if (isset($_POST['id_node']))
				doUpdate();
			else
				doInsert();
			$db->commit();
		}
		catch (PDOException $pe) {
			$db->rollBack();
			die('error');
		}
	}
	break;
case 'DELETE':
	if (isset($_GET['alpha'])) {
		$sql = 'DELETE FROM edge WHERE alpha=? AND beta=?';
		$st = $db->prepare($sql);
		$st->execute(array($_GET['alpha'], $_GET['beta']));
	}
	else {
		$st = $db->prepare('UPDATE node SET is_deleted=1 WHERE id_node=?');
		$st->execute(array($_GET['id_node']));
	}
}
