<div class="page-header">
<?php
	$redir_value = "/auth";
	if (get("redirect_to")) {
		$redir_value = $_GET["redirect_to"];
	}


	$error = "";
	if (isset($_GET["err"])):
		switch ($_GET["err"]) {
			case 'noadmin':
				$error = '<strong>Fel:</strong> Denna funktionen kräver administrationsrättigheter.';
				break;
			case '1':
				$error = '<strong>Fel:</strong> Du har angivit felaktiga inloggningsuppgifter.';
				break;
			default:
				$error = '<strong>Okänt fel:</strong> "' . $_GET["error"] . '"';
		}
		?>
		<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?= $error ?></div>
	<?php endif ?>
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
		 <form role="form" class="form-horizontal" method="post" action="/auth/login.php?redirect_to=<?= $redir_value?>">
			<input type="hidden" name="redirect_to" value="<?= $redir_value?>" />
			<?php
				form_control("username", "CID", "input", "user", true);
				form_control("password", "Lösenord", "password", "lock", false);
			?>
			<div class="form-group">
	<div class="col-lg-offset-2 col-lg-10">
		<button type="submit" class="btn btn-primary" name="intent" value="login">Logga in</button>
		<a href="/auth/?page=reset" class="btn btn-default">Återställ lösenord</a>
	</div>
			</div>
		 </form>
		</div>
	<?php else: ?>
	<div class="col-lg-4">
		<p>Beskrivande text för inloggade användare</p>
		<form action="/auth/resetpass.php" method="post">
			
		</form>
		<a href="/auth/?page=reset"
	</div>
	<?php endif; ?>
</div>
