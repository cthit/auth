<!DOCTYPE html>
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
      <div class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
	  <div class="navbar-header">
	    <a class="navbar-brand" href="/">Chalmers IT Autentisering</a>
	  </div>
	  <div class="navbar-collapse collapse">
	    <ul class="nav navbar-nav">
	      <li><a href="/">Hem</a></li>
	      <li><a href="/?page=reset">Återställ lösenord</a></li>
	    </ul>
	    <div class="navbar-form navbar-right">
	      <?php if (is_signed_in()):
		if (is_admin()): ?>
	      <a href="admin.php" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-wrench"></span> Admin</a>
		<?php endif; ?>

	      <a href="logout.php" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span> Logga ut</a>
	      <?php endif; ?>
	    </div>
	  </div>
	</div>
      </div>
