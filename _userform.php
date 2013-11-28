<?php global $username, $email;

if (isset($_GET["redirect_to"])) {
	$redirect = $_GET["redirect_to"];
} else {
	$redirect = $_SERVER['HTTP_REFERER'];
} ?><div class="page-header">
	<div class="container">
		<h1>Har du ingen användare på chalmers.it än?</h1>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		<p>Fyll i dina uppgifter i tabellen till höger för att skapa en.
		Med en användarprofil på chalmers.it kan du ta del av de tjänster som vi erbjuder.</p>
		<img src="digit2.png" alt="digITsmurfen">
	</div>
	<div class="col-lg-6 col-lg-offset-2">
		<form role="form" id="ldap-create-user-form" class="form-horizontal" method="post" action="createUser.php?redirect_to=<?=urlencode($redirect)?>">
			<div class="form-group">
				<label for="username" class="col-lg-4 control-label">CID:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="username" name="username" required class="form-control" type="text" value="<?= $username ?>" placeholder="CID"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="password" class="col-lg-4 control-label">Chalmerslösenord:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="password" name="password" required class="form-control" type="password" value="<?= $password ?>" placeholder="Ditt chalmerslösnord"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="nick" class="col-lg-4 control-label">Nick:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="nick" name="nick" required class="form-control" type="text" placeholder="Nick på IT"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-lg-4 control-label">Mail:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="email" name="email" required class="form-control" type="email" value="<?= $email ?>"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="new_password" class="col-lg-4 control-label">Välj nytt lösenord:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="new_password" name="new_password" required class="form-control" type="password"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="verify_password" class="col-lg-4 control-label">Bekräfta lösenord:</label>
				<div class="col-lg-8">
					<div class="input-group col-lg-10">
						<input id="verify_password" name="verify_password" required class="form-control" type="password"/>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-8 col-lg-offset-4">
					<div class="input-group col-lg-10">
						<button type="submit" required class="btn btn-primary" name="intent" value="login">Skapa användare</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
