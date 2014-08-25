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
require("functions.php");

$ldap = new ldap($cid);
$auth = new auth();

if (($ldap->askChalmers(true) || $ldap->askChalmers(false) && $auth->isWhitelisted($cid))
&& $ldap->authChalmers($old_passwd)) {
	$userdata = array(
		"cid" => $cid,
		"pass" => $new_passwd,
		"nick" => $nick,
		"email" => $email,
		"admission_year" => $_POST["admission_year"],
		"accept_terms" => $_POST["accept_terms"] == "on" ? "TRUE" : "FALSE",
		"nollan_photo" => search_image($cid)
	);
	$error = $ldap->addUser($userdata);
	if ($error)
		throw new Exception("didnt work");
	$auth->addToken($cid);
	$auth->removeFromWhitelist($cid);
	if (!empty($redirect)) {
		header("Location: ".$redirect);
	}
}

