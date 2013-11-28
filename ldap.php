<?php

require "config.php";

/**
* This class is doing all the hard work related to LDAP
*/
class ldap {

	private $dn;
	private $host;
	private $user;
	private $chalmers_data;

	public function __construct($user) {
		$this->dn = 'DC=chalmers,DC=it';
		$this->host = '192.168.0.8';
		$this->user = $user;
	}

	private function connect() {
		$ldap_handle = ldap_connect($this->host);
	
		if(!$ldap_handle) {
			throw new Exception('No connection to the server');
		}

		ldap_set_option($ldap_handle, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_handle, LDAP_OPT_REFERRALS, 0);

		return $ldap_handle;
	}

	/**
	* Verify a user's existance
	*/
	public function user_exists() {
		$user = $this->search("(uid=".$this->user.")");
		return $user !== false;
	}

	/**
	* Log in with the user to validate the password
	*/
	public function authenticate($password) {
		$ldap_handle = $this->connect();

		$isSuccess = @ldap_bind($ldap_handle, 'uid='.$this->user.',cn=users,'.$this->dn, $password);
		ldap_unbind($ldap_handle);

		return $isSuccess;
	}

	public function generateForm($password, $redirect) {
		//echo "User is not in local LDAP db, but has successfully authenticated with Kerberos!";
		// Add user to LDAP
		$username = $this->user;
		$email = $this->chalmers_data[0]["mail"][0]; // mail
		$_GET["page"] = "create";
		require('index.php');
	}

	/**
	* Search for user in LDAP by cid (wrapper function)
	*/
	public function search_by_cid($cid) {
		return $this->search("(uid=".$cid.")");
	}

	/**
	* Search for user in LDAP 
	*/
	public function search($search_filter) {
		$ldap_handle = $this->connect();

		$username = LDAP_SEARCH_USER;
		$password = LDAP_SEARCH_PASS;

		$attributes = array('givenname','sn','mail','uidNumber','uid','displayName');
		ldap_bind($ldap_handle, 'cn='.$username.','.$this->dn, $password);

		$search_result = ldap_search($ldap_handle, 'cn=users,'.$this->dn, $search_filter, $attributes);
		$users = ldap_get_entries($ldap_handle, $search_result);

		if ($users["count"] === 0) {
			ldap_unbind($ldap_handle);
			return false;
		}

		$search_result = ldap_search($ldap_handle, 'ou=groups,'.$this->dn, "(cn=*)", array("cn", "memberuid"));
		$groups_entries = ldap_get_entries($ldap_handle, $search_result);

		ldap_unbind($ldap_handle);
		foreach($users as $user) {
			if(is_array($user)) {
				$user_groups = array();
				$cid = $user["uid"][0];
				foreach($groups_entries as $group) {
					if (in_array($cid, $group["memberuid"])) {
						$user_groups[] = $group["cn"][0];
					}
				}

				$result[] = array(
				"cid" => $user["uid"][0],
				"dn" => $user["dn"],
				"firstname" => $user["givenname"][0],
				"lastname" => $user["sn"][0],
				"mail" => $user["mail"][0],
				"nick" => $user["displayname"][0],
				"uidnumber" => $user["uidnumber"][0],
				"groups" => $user_groups
				);
			}
		}

		if(sizeof($result) == 1 ) {
			$result = $result[0];
		}

		return $result;
	}

	/**
	* Get userinfo
	*/
	public function get_userinfo($cid) {
		return $this->search("(uid=$cid)");
	}

	/**
	* Add user to LDAP
	*/
	public function addUser($email, $nick, $new_passwd) {
		$username = LDAP_ADMIN_USER;
		$password = LDAP_ADMIN_PASS;

		$ldap_handle = $this->connect();

		ldap_bind($ldap_handle, 'cn=admin,dc=chalmers,dc=it', $password);
		
		$base_dn = "cn=users," . $this->dn;

		$sr = ldap_search($ldap_handle, $base_dn, "uidnumber=*", array("uidnumber"));
		
		$users = ldap_get_entries($ldap_handle, $sr);

		$max = 0;
		foreach($users as $user) {
			if ($user["uidnumber"][0] > $max)
				$max = $user["uidnumber"][0]; 
		}

		$user = $this->userArray($email, $nick, $new_passwd, $max+1);
		ldap_add($ldap_handle, "uid=" . $this->user.",".$base_dn, $user);
		
		$error = ldap_error($ldap_handle);
		ldap_unbind($ldap_handle);
		return $error === 0;
	}

	/**
	* Construct an array with user details
	*/
	private function userArray($mail, $nick, $password, $uid) {
		if ($this->chalmers_data == NULL) {
			throw new Exception("Must call 'authChalmers' before");
		}
		$user = array();
		$user["uid"] = $this->user; // cid
		$user["cn"] = $this->chalmers_data[0]["cn"][0]; // firstname lastname
		$user["sn"] = $this->chalmers_data[0]["sn"][0]; // lastname
		$user["givenname"] = $this->chalmers_data[0]["givenname"][0]; // firstname
		$user["mail"] = $mail;
		$user["displayname"] = $nick;
		
		$user["objectClass"] = array("inetOrgPerson", "posixAccount", "top");
		$user["homeDirectory"] = "/home/chalmersit/$this->user";
		$user["loginShell"] = "/bin/bash";
		$user["userPassword"] = $this->generatePassword($password);
		$user["uidNumber"] = $uid;
		$user["gidNumber"] = 502;

		return $user;
	}

	/**
	* Generate password
	*/
	private function generatePassword($userpass) {
		$salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
		return "{SSHA}" . base64_encode(sha1($userpass . $salt, true) . $salt);
	}

	/**
	* Replace the old password for a user.
	*/
	public function changePassword($newPassword) {
		$username = LDAP_ADMIN_USER;
		$password = LDAP_ADMIN_PASS;

		$ldap_handle = $this->connect();

		ldap_bind($ldap_handle, 'cn=admin,dc=chalmers,dc=it', $password);

		$replace = array('userpassword' => $this->generatePassword($newPassword));
		$result = ldap_modify($ldap_handle, 'uid='.$this->user.',cn=users,dc=chalmers,dc=it', $replace);

		ldap_unbind($ldap_handle);	
		return $result;
	}

	/**
	* Purge ldap from enemies
	*/
	public function removeUser($cid) {
		// TODO: Do stuff
	}

	public function askChalmers($it = true) {
		// Ask Kerberos about the lost souls roaming the Underworld.
		//TODO: Batch in old users in some way, register?
		$ds = ldap_connect("ldap://ldap.chalmers.se");
		if ($ds) {
			$result = NULL;
			if($it) {
				$res = ldap_search($ds, "ou=people,dc=chalmers,dc=se","(uid=$this->user)");
				$this->chalmers_data = $result = ldap_get_entries($ds, $res);
			} else {
				$filter = '(cn=pr_ch_tkite)';
				$search_result = ldap_search($ds, "ou=groups,dc=chalmers,dc=se", $filter, array("member"));
				$info = ldap_get_entries($ds, $search_result);
				foreach($info[0]['member'] as $student) {
					if(preg_match('/uid\='.$this->user.',/', $student)) {
						$res = ldap_search($ds, "ou=people,dc=chalmers,dc=se","(uid=$this->user)");
						$this->chalmers_data = $result = ldap_get_entries($ds, $res);
						break;
					}
				}
			}
			ldap_close($ds);
			return $result;
		}
	}

	/**
	* Log in with the user to Chalmers to validate the password
	*
	* Requires ubuntu package "krb5-user" (set default domain to "CHALMERS.SE")
	*/
	public function authChalmers($password) {
		$uname = escapeshellarg($this->user);
		$passwd = escapeshellarg($password);
		$command = "echo $passwd | kinit $uname";
		exec($command, $op, $out);

		if ($out > 1) {
			throw new Exception("Unknown error: kinit returned $out");
		} else { // ($out === 0)
			exec("kdestroy");
		}

		return $out === 0; // 0 in bash means success

	}
}

