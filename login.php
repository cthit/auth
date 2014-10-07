<?php

require_once 'common.php';

if (post('username') && post('password')) {
	$username = htmlentities(post('username'));
	$password = post('password');
} else if (post('log') && post('pwd')) {
	$username = htmlentities(post('log'));
	$password = post('pwd');
	$wp = true;
} else {
	die('Not enough parameters to login!');
}

require_once('ldap.php');
require_once('auth.php');

$auth = new auth();
$ldap = new ldap($username);

function redirect($success) {
	$url = post("redirect_to") ? post("redirect_to") : $_SERVER["HTTP_REFERER"];
	if (!$success) {
		$url = "/?err=1&redirect_to=" . $url;
	}
	header("Location: $url");
}

if ($ldap->user_exists()) {
	if($ldap->authenticate($password)) { // Användaren loggas in med korrekta uppgifter
		$auth->addToken($username);
		redirect(true);
	} else {
		redirect(false);
	}
} else if ($auth->isWhitelisted($username) && $ldap->authChalmers($password)) {
	$ldap->generateForm($password, get("redirect_to")?get("redirect_to"):$_SERVER["HTTP_REFERER"]);
} else if ($ldap->askChalmers() && $ldap->authChalmers($password)) { // Användaren finns ej i vår LDAP, men authar mot Chalmers
	$ldap->generateForm($password, get("redirect_to")?get("redirect_to"):$_SERVER["HTTP_REFERER"]);
} else { // Fel användare eller lösenord
	redirect(false);
	die("Invalid username or password");
}
