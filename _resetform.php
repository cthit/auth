<div class="page-header">
	<?php
	$type = "danger";
	$error = "";
	if (isset($_GET["msg"])):
		switch ($_GET["msg"]) {
			case 'success':
				$type = 'success';
				$error = '<strong>Lyckades!</strong> Kolla din inkorg!';
				break;
			case 'fail':
				$error = '<strong>Fel:</strong> Felaktigt CID.';
				break;
			default:
				$error = '<strong>Okänt fel:</strong> "' . $_GET["error"] . '"';
		}
		?>
		<div class="alert alert-<?= $type ?>"><span class="glyphicon glyphicon-warning-sign"></span> <?= $error ?></div>
	<?php endif; ?>
	<div class="container">
		<h1>Återställ ditt lösenord!</h1>
	</div>
</div>
<div class="row">
	<?php if (!is_signed_in()): ?>
	<div class="col-lg-4">
		<p>Ett mail kommer att skickas till din angivna mailadress med vidare instruktioner för återställning!</p>
		<p><em>Kontakta digIT om du skulle du behöva ändra din mailadress.</em></p>
	</div>
		<div class="col-lg-5 col-lg-offset-3">
		<?php if (!isset($_GET['token'])): ?>
			<form role="form" class="form-horizontal" method="post" action="/auth/resetpass.php">
				<input type="hidden" name="redirect_to" value="/auth" />
				<?php
					form_control("username", "CID", "input", "user", true);
				?>
				<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" class="btn btn-danger" name="intent" value="reset">Återställ</button>
		</div>
				</div>
			</form>
		<?php else: ?>
			<form role="form" class="form-horizontal" method="post" action="/auth/resetpass.php">
				<input type="hidden" name="token" value="<?= $_GET['token'] ?>">
				<?php
					form_control("password", "Lösenord", "password", "lock", true);
					form_control("confirm-password", "Bekräfta", "password", "lock", true);
				?>
				<button type="submit" class="btn btn-primary" name="intent" value="confirm">Återställ lösenord</button>
		</div>
	<?php else: ?>
	<div class="col-lg-4">
		<p>Beskrivande text för inloggade användare</p>
	</div>
	<?php endif; ?>
</div>
