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
$message = <<<MAIL
Hej {$user["firstname"]}!

För att återställa ditt lösenord, klicka på följande länk: <a href="https://chalmers.it/auth/?page=reset&token=$token">https://chalmers.it/auth/?page=reset&token=$token</a>
MAIL;

	$res = mail($user["mail"], "Chalmers IT Lösenordsåterställning", $message, implode($headers, "\r\n"));
	header("Location: https://chalmers.it/auth/?page=reset&msg=success");

} else if (isset($_GET["token"])) { ?>
	<form method="post" action="resetpass.php">
		<input name="password" type="password" />
		<input name="confirm-password" type="password" />
		<input type="hidden" name="token" value="<?=$_GET["token"]?>" />
		<input type="submit" />
	</form>
<?php } else if (isset($_POST["password"], $_POST["confirm-password"], $_POST["token"]) || isset($_POST["password"], $_POST["cookie"])) {
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

	$result = $ldap->changePassword($_POST["password"], $_POST["confirm-password"]);
	if ($result) {
		$auth->clearResetToken($cid);
		echo "Changed password successfully.";
	} else {
		echo "Password reset failed.";
	}
}
