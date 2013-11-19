<?php

class auth {

	private $cookieName;
	private $cookieDomain;
	private $cookiePath;
	private $onlySSL;

	function __construct() {
		$this->cookieName = 'chalmersItAuth';
		$this->cookieDomain = '.chalmers.it';
		$this->cookiePath = '/';
		$this->onlySSL = false;
	}

	public function addToken($username) {
		$cookieExpire = time() + 31104000; //60*60*24*30*12;
		$token = sha1(uniqid(rand(), true));

		$query1 = 'DELETE FROM authToken WHERE username = "'. $username .'"';                                                                                              
		$query2 = 'INSERT INTO authToken (token, username) VALUES ("'.$token.'", "'.$username.'")';

		setCookie($this->cookieName, $token, $cookieExpire, $this->cookiePath, $this->cookieDomain, $this->onlySSL);
		$this->query(array($query1, $query2));
	}
	
	public function addResetToken($username) {
		$token = sha1(uniqid(rand(), true));

		$query1 = 'DELETE FROM resetToken WHERE username = "'. $username .'"';                                                                                              
		$query2 = 'INSERT INTO resetToken (token, username) VALUES ("'.$token.'", "'.$username.'")';

		$res = $this->query(array($query1, $query2));
		return $token;
	}
	
	public function clearResetToken($username) {
		$query = 'DELETE FROM resetToken WHERE username = "'. $username .'"';
		$this->query(array($query));
	}

	public function isWhitelisted($username) {
		//Fråga databasen
	}

	public function addToWhitelist($username) {

	}

	public function removeFromWhitelist($username) {

	}

	public function getUsername($token, $table="authToken", $date = null) {
		$query = 'SELECT username FROM '.$table.' WHERE token = "' .$token. '"' . (isset($date)? ' AND DATEDIFF( NOW( ) , timestamp ) <=7':'');
		return $this->query(array($query));
	}

	public function removeToken($token) {
		$query = 'DELETE FROM authToken WHERE token = "' .$token. '"';

		setcookie($this->cookieName, '', time()-3600, $this->cookiePath, $this->cookieDomain, $this->onlySSL);
		$this->query(array($query));
	}

	private function query($queries) {
		$host = 'localhost';
		$dbUser = 'auth';
		$dbPass = 't8vRfWRUyVqVqB4a';
		$db = 'auth';

		$con = mysqli_connect($host, $dbUser, $dbPass, $db);

		if (mysqli_connect_errno($con)) {
			echo 'Failed to connect to database: contact digIT';
			return;
		}

		foreach($queries as $query) {
			$response = mysqli_query($con, $query);
		}

		mysqli_close($con);

		return mysqli_fetch_array($response);
	}
}

