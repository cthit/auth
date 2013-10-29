<?php

function is_signed_in() {
	$token = $_COOKIE["chalmersItAuth"];
	include "auth.php";
	$auth = new auth();
	return isset($auth->getUsername($token));
}

function is_admin() {
	return false; // Not implemented yet
}

function render($filename) {
	include "_$filename.php";
}