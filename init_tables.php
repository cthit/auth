<?php
require "config.php";

function init_auth_tables() {
	$host = 'localhost';
	$dbUser = DB_USER;
	$dbPass = DB_PASS;
	$db = DB_USER;
	$con = mysqli_connect($host, $dbUser, $dbPass, $db);

	mysqli_query($con, "create table `authToken` (token char(40), username varchar(30) not null primary key)");
	mysqli_query($con, "create table `resetToken` (token char(40), username varchar(30) not null primary key, timestamp timestamp)");
	mysqli_query($con, "create table `whitelist` (cid varchar(30) primary key)");
}
init_auth_tables();