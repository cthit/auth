<?php require 'functions.php'; 
if (isset($_POST['intent']) && $_POST['intent'] == 'whitelist') {
	require 'auth.php';
	$auth = new auth();
	$cid = $_POST['cid'];
	if (ctype_alpha($cid)) {
		$auth->addToWhitelist($cid);
		$notice = "\"$cid\" har lagts till i whitelist.";
		global $notice;
	}
}
?><!DOCTYPE html>
<html class="no-js">
		<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
				<title>Chalmers IT Autentisering</title>
				<meta name="description" content="">
				<meta name="viewport" content="width=device-width">
				<link rel="stylesheet" href="css/bootstrap.min.css">
				<link rel="stylesheet" href="css/main.css">
				<!--[if lt IE 9]>
						<script src="js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
				<![endif]-->
		</head>
		<body>
			<?php render('header'); ?>
			<div class="container">
				<?php render('createuser'); ?>
				<hr>
				<footer>
					<p>&copy; <a href="http://digit.chalmers.it/">digIT</a> 2013</p>
				</footer>
			</div>
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
			<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
			<script src="js/vendor/bootstrap.min.js"></script>
			<script src="js/main.js"></script>
		</body>
</html>
