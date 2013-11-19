<?php

if(!empty($_COOKIE['chalmersItAuth'])) {
	require_once('auth.php');

	$token = htmlentities($_COOKIE['chalmersItAuth']);

	$auth = new auth();
	$auth->removeToken($token);

	if(!empty($_SERVER['HTTP_REFERER'])) {
        	$referer = $_SERVER['HTTP_REFERER'];
        	header("location: $referer");
	}
	
} else {
	die('No cookie for ma\' wookie');
}

