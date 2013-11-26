<?php

if(isset($_POST['username'], $_POST['password'])) { 
	$user = htmlentities($_POST['username']);
	$password = $_POST['password'];
} else if(isset($_POST['log'], $_POST['pwd'])) { 
	$user = htmlentities($_POST['log']);
	$password = $_POST['pwd'];
	$wp = true;
}

require_once('ldap.php');
require_once('auth.php');

$auth = new auth();
$ldap = new ldap($user);

function redirect($success) {
	$url = isset($_POST["redirect_to"]) ? $_POST["redirect_to"] : $_SERVER["HTTP_REFERER"];
	$purl = parse_url($url);
	$redirect = $purl["scheme"] . "://" . $purl["host"];
	if ($success) {
		$redirect .= isset($purl["path"]) ? $purl["path"]:"" . (!empty($purl["query"]) ? "?" . $purl["query"]:"");
	} else {
		$redirect .= "/loggain/?err=1";
	}
	header("Location: $redirect");
}

if ($ldap->user_exists()) {
	if($ldap->authenticate($password)) { // Användaren loggas in med korrekta uppgifter
		$auth->addToken($user);
		redirect(true);
	}
} else if ($auth->isWhitelisted($user) && $ldap->authChalmers($password)) {
	$ldap->generateForm($password, isset($_GET["redirect_to"])?$_GET["redirect_to"]:$_SERVER["HTTP_REFERER"]);
} else if ($ldap->askChalmers() && $ldap->authChalmers($password)) { // Användaren finns ej i vår LDAP, men authar mot Chalmers
	$ldap->generateForm($password, isset($_GET["redirect_to"])?$_GET["redirect_to"]:$_SERVER["HTTP_REFERER"]);
} else { // Fel användare eller lösenord
	redirect(false);
	die("Invalid username or password");
}
