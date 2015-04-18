<?php
require_once('config.php');
$fields = array(
	'node' => array('name', 'description'),
	'profile' => array('email'),
	'gps' => array('latitude', 'longitude'),
	'url' => array('link'),
	'file'); //test this

function current_user() {
	//FIXME
	return 4;
}

function fromPost($k) {
	return $_POST[$k];
}

function doUpdate() {
	//TODO just an insert and a link
	$st = $db->prepare('INSERT INTO edge VALUES(?, '.doInsert().')');
	$st->execute(array($_POST['id_node']));
}

/*function doUpdate() {
	global $db, $fields;
	foreach ($fields as $k=>$v) {
		if (in_array($v[0], $_POST)) {
			$sql = "UPDATE $k SET ".join('=?, ', $v).'=? WHERE id_node=?';
			$st = $db->prepare($sql);
			$st->execute(array_map('fromPost', $v)+array($_POST['id_node']));
			break; //next table
		}
	}
}*/

function doInsert() {
	global $db, $fields;
	foreach ($fields as $k=>$v) {
		if (in_array($v[0], $_POST)) {
			$sql = "INSERT INTO $k(".join(', ', array('id_node')+$v).') VALUES(';
			$sql .= join(', ', array_fill(0, sizeof($v), '?')).')';
			$st = $db->prepare($sql);
			$st->execute(array($_POST['id_node'])+array_map('fromPost', $v));
			return $db->lastInsertId();
		}
	}
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
		$st->execute(array($_POST['alpha'], $_POST['beta']));
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
	if (isset($_POST['alpha'])) {
		$sql = 'DELETE FROM edge WHERE alpha=? AND beta=?';
		$st = $db->prepare($sql);
		$st->execute(array($_POST['alpha'], $_POST['beta']));
	}
	else {
		$st = $db->prepare('UPDATE node SET is_deleted=1 WHERE id_node=?');
		$st->execute(array($_POST['id_node']));
	}
}

/*foreach ($db->query('SELECT * FROM node') as $r)
	echo json_encode($r);*/
