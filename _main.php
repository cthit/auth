<div class="page-header">
<?php
	$redir_value = "/";
	if (get("redirect_to")) {
		$redir_value = get("redirect_to");
	}


	$error = "";
	if (get("err")):
		switch (get("err")) {
			case 'noadmin':
				$error = '<strong>Fel:</strong> Denna funktionen kräver administrationsrättigheter.';
				break;
			case '1':
				$error = '<strong>Fel:</strong> Du har angivit felaktiga inloggningsuppgifter.';
				break;
			default:
				$error = '<strong>Okänt fel:</strong> "' . get("error") . '"';
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
		<p>Här loggar du in och styr ditt chalmers.it konto. Har du inte ett konto så påbörjar du skapningen genom att logga in med ditt cid och cid-lösenord, det som du använder när du loggar in på exempelvis Studentportalen.</p>
	</div>
		<div class="col-lg-5 col-lg-offset-3">
		 <form role="form" class="form-horizontal" method="post" action="/login.php?redirect_to=<?= $redir_value?>">
			<input type="hidden" name="redirect_to" value="<?= $redir_value?>" />
			<?php
				form_control("username", "CID", "input", "user", true);
				form_control("password", "Lösenord", "password", "lock", false);
			?>
			<div class="form-group">
	<div class="col-lg-offset-2 col-lg-10">
		<button type="submit" class="btn btn-primary" name="intent" value="login">Logga in</button>
		<a href="/?page=reset" class="btn btn-default">Återställ lösenord</a>
	</div>
			</div>
		 </form>
		</div>
	<?php else: ?>
	<div class="col-lg-4">
		<table>
			<th>Applikationer som kräver inloggning</th>
			<tr><td><a href="https://bookit.chalmers.it"><h4>bookIT</h4></a></td></tr>
			<tr><td><a href="https://hubbit.chalmers.it"><h4>hubbIT</h4></a></td></tr>
		</table>
		<form action="/resetpass.php" method="post">
			
		</form>
		<a href="/?page=reset">Återställ ditt lösenord här</a>
	</div>
	<?php endif; ?>
</div>
