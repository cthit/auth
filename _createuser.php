<div class="page-header">
	<?php 
	global $notice;
	if (isset($notice)) { ?>
		<div class="alert alert-success"><?= $notice ?></div>
	<?php } ?>
	<div class="container">
	<h1>Skapa ett konto till Chalmers IT!</h1>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		<p>Har du ingen användare på chalmers.it än?</p>
		<p>Fyll i dina uppgifter i tabellen till höger för att skapa en.
		Med en användarprofil på chalmers.it kan du ta del av de tjänster som vi erbjuder.</p>
	</div>
	<div class="col-lg-5 col-lg-offset-3">
		<h3>Whitelista användare</h3>
		<form role="form" class="form-horizontal" method="post">
			<?php form_control("cid", "CID", "input", null, true); ?>
			<div class="col-lg-offset-2 col-lg-10">
				<button type="submit" class="btn btn-default" name="intent" value="whitelist">Add user to whitelist</button>
			</div>
		</form>
	</div>
</div>