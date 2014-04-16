<?php
function if_isset($map, $key = 'token') {
	return isset($map[$key]) && htmlentities($map[$key]);
}
$token = if_isset($_POST) || if_isset($_GET) || if_isset($_COOKIE, 'chalmersItAuth') || false;
$cid = if_isset($_GET, 'cid') || false;
if (!$token && !$cid) {
	die('Need more params, read the documentation for more info');
}

require_once('ldap.php');
require_once('auth.php');

$auth = new auth();
$ldap = new ldap('***REMOVED***');

$user = false;
if ($token) {
	$tokenUser = $auth->getUsername($token);
	$user = $tokenUser && $tokenUser['username'];
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

