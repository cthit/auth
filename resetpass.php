<?php
if (isset($_POST["username"])) {
	$cid = $_POST["username"];

	require("ldap.php");
	require("auth.php");

	$ldap= new ldap($cid);
	$auth = new auth();

	$user = $ldap->get_userinfo($cid);
	$token = $auth->addResetToken($cid);

	$headers = array(
		"From: no-reply@chalmers.it"
	);

	$res = mail($user["mail"], "Chalmers IT Lösenordsåterställning", "Hej ". $user["firstname"] . "!\n\nFör att återställa ditt lösenord, klicka på följande länk: https://chalmers.it/auth/resetpass.php?token=$token", implode($headers, "\r\n"));

	if (!isset($_POST["no-redirect"]) || $_POST["no-redirect"] == "false") {
		header("Location: http://beta.chalmers.it/loggain/?checkemail=confirm");
	} else {
		echo "Check inbox!";
	}
} else if (isset($_GET["token"])) { ?>
	<form method="post" action="resetpass.php">
		<input name="password" type="password" />
		<input type="hidden" name="token" value="<?=$_GET["token"]?>" />
		<input type="submit" />
	</form>
<?php } else if (isset($_POST["password"], $_POST["token"]) || isset($_POST["password"], $_POST["cookie"])) {
	require("ldap.php");
	require("auth.php");
	$auth = new auth();
	
	if (isset($_POST["token"])) {
		$token = $_POST["token"];
		$cid = $auth->getUsername($token, "resetToken", "use timelimit")[0];
	} else if (isset($_POST["cookie"])) {
		$token = $_POST["cookie"];
		$cid = $auth->getUsername($token)[0];
	}


	$ldap = new ldap($cid);

	$result = $ldap->changePassword($_POST["password"]);
	if ($result) {
		$auth->clearResetToken($cid);
		echo "Changed password successfully.";
	} else {
		echo "Password reset failed.";
	}
}
