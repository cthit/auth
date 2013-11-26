<?php require 'functions.php';
	render('header'); ?>
	<div class="container"><?php
		if (isset($_GET['page']) && $_GET['page'] == 'create') {
			render('userform');
		} else {
			render('main');
		} ?><hr>
		<footer>
			<p>&copy; <a href="http://digit.chalmers.it/">digIT</a> 2013</p>
		</footer>
	</div>
<?php render('footer'); ?>