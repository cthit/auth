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

För att återställa ditt lösenord, klicka på följande länk: https://chalmers.it/auth/?page=reset&token=$token
MAIL;

	$res = mail($user["mail"], "Chalmers IT Lösenordsåterställning", $message, implode($headers, "\r\n"));
	header("Location: https://chalmers.it/auth/?page=reset&msg=sent");

} else if (isset($_POST["password"], $_POST["confirm-password"], $_POST["token"]) || isset($_POST["password"], $_POST["cookie"])) {
	require("ldap.php");
	require("auth.php");
	$auth = new auth();

	if (isset($_POST["token"])) {
		$token = $_POST["token"];
		$cid = $auth->getUsername($token, "resetToken", "use timelimit")[0];
		$samePasswd = $_POST["password"] === $_POST["confirm-password"];
	} else if (isset($_POST["cookie"])) {
		$token = $_POST["cookie"];
		$cid = $auth->getUsername($token)[0];
		$samePasswd = true;
	}


	$ldap = new ldap($cid);

	$result = $samePasswd && $ldap->changePassword($_POST["password"]);
	if ($result) {
		$auth->clearResetToken($cid);
		# Password was reset
		header("Location: https://chalmers.it/auth/?page=reset&msg=success");
	} else {
		# Password was not reset
		header("Location: https://chalmers.it/auth/?page=reset&msg=fail");
	}
}
