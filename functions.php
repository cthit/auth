<?php
global $user, $digit;

function is_signed_in() {
	global $user;
	if (!isset($user)) {
		sign_in();
	}
	return isset($user);
}

function set_signed_in() {
	global $user, $digit;
	$token = $_COOKIE["chalmersItAuth"];
	$user_data = file_get_contents("https://chalmers.it/auth/userInfo.php?token=" . $token);
	$user = json_decode($user_data);
	$digit = in_array("digit", $user["groups"]);
}

function is_admin() {
	global $digit;
	if (!isset($user)) {
		sign_in();
	}
	return $digit;
}

function render($filename) {
	include "_$filename.php";
}