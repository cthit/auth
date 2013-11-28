<div class="page-header">
	<?php
	$error = "";
	if (isset($_GET["error"])):
		switch ($_GET["error"]) {
			case 'noadmin':
				$error = '<strong>Fel:</strong> Denna funktionen kräver administrationsrättigheter';
				break;
			default:
				$error = '<strong>Okänt fel:</strong> "' . $_GET["error"] . '"';
				break;
		}
		?>
		<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?= $error ?></div>
	<?php endif; ?>
	<div class="container">
		<h1>Välkommen till IT:s Autentiseringssystem!</h1>
	</div>
</div>
<div class="row">
	<?php if (!is_signed_in()): ?>
	<div class="col-lg-4">
		<p>Beskrivande text för ej inloggade användare</p>
	</div>
		<div class="col-lg-5 col-lg-offset-3">
		 <form role="form" class="form-horizontal" method="post" action="/auth/login.php">
			<?php
				form_control("username", "CID", "input", "user", true);
				form_control("password", "Lösenord", "password", "lock", false);
			?>
			<div class="form-group">
	<div class="col-lg-offset-2 col-lg-10">
		<button type="submit" class="btn btn-primary" name="intent" value="login">Logga in</button>
	</div>
			</div>
		 </form>
		</div>
	<?php else: ?>
	<div class="col-lg-4">
		<p>Beskrivande text för inloggade användare</p>
	</div>
	<?php endif; ?>
</div>