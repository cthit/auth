<?php

function get($var) {
	return isset($_GET[$var]) ? $_GET[$var] : false;
}
function post($var) {
	return isset($_POST[$var]) ? $_POST[$var] : false;
}