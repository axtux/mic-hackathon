<?php
require_once('lib.php');
$_SESSION['ch'] = $ch = md5(time().$_SERVER['REMOTE_ADDR'].'secret!');
?><!DOCTYPE html>
<!--Taken from http://getbootstrap.com/examples/signin/-->
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>Signin Template for Bootstrap</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/signin.css" rel="stylesheet">
</head>
<body>
<div class="container">
<div class="alert alert-info" role="alert">
	Demo account : you@me.com / bonjour
</div>
<form class="form-signin" action="api.php" method="post" onsubmit="dochap('<?=$ch?>');">
	<h2 class="form-signin-heading">Please sign in</h2>
	<label for="e" class="sr-only">Email address</label>
	<input name="ml" type="email" id="e" class="form-control" placeholder="Email address" required autofocus>
	<label for="p" class="sr-only">Password</label>
	<input name="pw" type="password" id="p" class="form-control" placeholder="Password" required>
	<div class="checkbox"><label><input type="checkbox" value="remember-me"> Remember me</label></div><!--TODO-->
	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>
<script type="text/javascript" src="js/sha1.js"></script>
<script type="text/javascript" src="js/login.js"></script>
</div>
</body>
</html>
