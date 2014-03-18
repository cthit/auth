<?php require 'functions.php';
if (!is_admin()) {
	header("Location: /auth/?err=noadmin");
}
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
render('header');?>
		<div class="container">
			<?php render('whitelist'); ?>
			<hr>
			<footer>
				<p>&copy; <a href="http://digit.chalmers.it/">digIT</a> 2014</p>
			</footer>
		</div>
<?php render('footer');
