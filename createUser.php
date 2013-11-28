<?php

if (!isset($_POST["password"])) {
	header("Location: " . $_SERVER["HTTP_REFERER"]);
}

$cid = $_POST["username"];
$old_passwd = $_POST["password"];
$email = $_POST["email"];
$nick = $_POST["nick"];
$new_passwd = $_POST["new_password"];
$redirect = $_GET["redirect_to"];

require("ldap.php");
require("auth.php");

$ldap = new ldap($cid);
$auth = new auth();

if (($ldap->askChalmers(true) || $ldap->askChalmers(false) && $auth->isWhitelisted($cid))
&& $ldap->authChalmers($old_passwd)) {
	$error = $ldap->addUser($email, $nick, $new_passwd);
	if ($error)
		throw new Exception("didnt work");
	$auth->addToken($cid);
	if (!empty($redirect)) {
		header("Location: ".$redirect);
	}
}

