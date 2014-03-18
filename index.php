<?php require 'functions.php';
	render('header'); ?>
	<div class="container"><?php
		switch($_GET['page']) {
			case 'create':
				render('userform');
				break;
			case 'reset':
				render('resetform');
				break;
			default:
				render('main');
		}?><hr>
		<footer>
			<p>&copy; <a href="http://digit.chalmers.it/">digIT</a> 2014</p>
		</footer>
	</div>
<?php render('footer'); ?>
