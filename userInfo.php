<?php

if(!isset($_POST['token'])) {
        if(!isset($_GET['token'])) {
        		if (!isset($_COOKIE['chalmersItAuth'])) {
                	die('Need more params, read the documentation for more info');
                } else {
                	$token = htmlentities($_COOKIE['chalmersItAuth']);
                }
        } else {
                $token = htmlentities($_GET['token']);
        }
} else {
        $token = htmlentities($_POST['token']);
}

require_once('ldap.php');
require_once('auth.php');

$auth = new auth();
$ldap = new ldap('***REMOVED***');

$tokenUser =  $auth->getUsername($token);
if(!$tokenUser['username']) {
	die('invalid token');
}

header("Content-Type: application/json");
echo json_encode($ldap->search('(uid='.$tokenUser['username'].')'));

