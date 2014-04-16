<?php
$key = 'token';

$token = isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : (isset($_COOKIE['chalmersItAuth']) ? $_COOKIE['chalmersItAuth'] : false));
$cid = isset($_GET['cid']) ? $_GET['cid'] : false;
if (!$token && !$cid) {
	die('Need more params, read the documentation for more info');
}

require_once('ldap.php');
require_once('auth.php');

$auth = new auth();
$ldap = new ldap('***REMOVED***');

$user = false;
if (!$cid) {
	$tokenUser = $auth->getUsername($token);
	$user = $tokenUser ? $tokenUser['username'] : null;
	if (!$user) {
		die('invalid token');
	}
}

$json = json_encode($ldap->search_by_cid($user));
if (isset($_GET['callback']) && $callback = $_GET['callback']) {
	header("Content-Type: application/javascript");
	echo "$callback($json);";
} else {
	header("Content-Type: application/json");
	echo $json;
}

