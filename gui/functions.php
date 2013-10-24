<?php

$demo = true;
$admin = true;

global $demo, $admin;

function is_signed_in() {
	global $demo;
    return $demo;
}

function is_admin() {
	global $admin;
	return $admin;
}

function render($filename) {
	include "_$filename.php";
}