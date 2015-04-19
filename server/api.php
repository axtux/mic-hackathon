<?php
require_once('lib.php');
$fields = array(
	'node' => array('name', 'description'),
	'profile' => array('email'),
	'gps' => array('latitude', 'longitude'),
	'url' => array('link'),
	'file'); //test this

function read_post($k) {
	return $_POST[$k];
}

function add_improvement($original, $new) {
	global $db;
	$st = $db->prepare('INSERT INTO edge VALUES(?, ?, ?)');
	$st->execute(array($original, $new, 'improvement'));
}

function do_update() {
	add_improvement($_POST['id_node'], do_insert());
}

function do_insert() {
	global $db, $fields;
	$id = create_node($_POST['name'], $_POST['description']);
	foreach ($fields as $k=>$v) {
		if (!empty($_POST[$v[0]]) && $k!='node') {
			$sql = "INSERT INTO $k(".join(', ', array_merge(array('id_node'),$v)).') VALUES(';
			$sql .= join(', ', array_fill(0, sizeof($v)+1, '?')).')';
			$st = $db->prepare($sql);
			$st->execute(array_merge(array($id),array_map('read_post', $v)));
			return $db->lastInsertId();
		}
	}
	return $id;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); //FIXME
switch ($_SERVER['REQUEST_METHOD']) {
case 'GET':
	if (isset($_GET['logout']))
		$_SESSION = array();
	else {
		if (isset($_GET['search'])) {
			$st = $db->prepare('SELECT id_node, name FROM node WHERE name LIKE ?');
			$st->execute(array($_GET['search'].'%'));
		}
		else {
			$id_node = (int)$_GET['id_node'];
			$sql = 'SELECT y.* FROM ('.
				'SELECT NULL AS alpha, NULL AS beta, NULL AS relation, x.* '.
				'FROM bignode x WHERE id_node=? UNION ALL '.
				'SELECT * FROM edge INNER JOIN bignode x ON '.
				'alpha=x.id_node WHERE beta=? UNION ALL '.
				'SELECT * FROM edge INNER JOIN bignode x ON '.
				'beta=x.id_node WHERE alpha=?'.
				') y'; //INNER JOIN profile u ON ?=u.id_node ';
				//'WHERE y.is_deleted=u.is_omniscient';
			$st = $db->prepare($sql);
			$st->execute(array($id_node, $id_node, $id_node));//, current_user()));
		}
		echo json_encode($st->fetchAll());
	}
	break;
case 'POST':
	if (isset($_POST['ml']))
		do_login($_POST['ml'], $_POST['pw'], $_SESSION['ch']);
	else if (!empty($_POST['relation'])) {
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
			if (isset($_FILES['file']) && $_FILES['file']['error']!=4) {
				$id = do_upload('file', $_POST['name'], $_POST['description']);
				if (!empty($_POST['id_node']))
					add_improvement($_POST['id_node'], $id);
			}
			else if (!empty($_POST['id_node']))
				do_update();
			else
				do_insert();
			$db->commit();
		}
		catch (UploadException $e) {
			$db->rollBack();
			die($e);
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
