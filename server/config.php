<?php
date_default_timezone_set('Europe/Brussels');

if ($_SERVER['SERVER_NAME'] == 'localhost')
	require_once('config.dev.php');
else
	require_once('config.prod.php');

define('UPLOAD_DIR', SITE_ROOT.'/uploads');
define('UPLOAD_MAX', 5 * 1024 * 1024); //20 MiB
$UPLOAD_FORBIDDEN = explode(',', 'exe,scr,bat,pif,com');

try {
	$db = getDb();
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (Exception $e) {
	die('db oops');
}
