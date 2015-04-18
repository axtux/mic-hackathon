<?php
define('SITE_TITLE', 'LOCAL');
define('SITE_BASE', 'http://localhost/mic');
define('SITE_ROOT', '/mic');

function getDb() {
	return new PDO('mysql:host=localhost;port=3306;dbname=yourdb;',
		'you', 'secret',
		array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
}
