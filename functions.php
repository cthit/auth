<?php

function is_signed_in($token) {
	include "auth.php";
	$auth = new auth();
	return !empty($auth->getUsername($token));
}

function is_admin() {
	return false; // Not implemented yet
}

function render($filename) {
	include "_$filename.php";
}