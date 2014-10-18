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
		$this->dn = 'dc=chalmers,dc=it';
		$this->host = LDAP_HOST;
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
		$user = $this->search("(uid={$this->user})");
		return $user !== false;
	}

	/**
	* Log in with the user to validate the password
	*/
	public function authenticate($password) {
		$user = $this->search_by_cid($this->user);

		$isSuccess = false;
		if ($user) {
			$isSuccess = @ldap_bind($ldap_handle, $user['dn'], $password);
			ldap_unbind($ldap_handle);
		}

		return $isSuccess;
	}

	public function generateForm($password, $redirect) {
		//echo "User is not in local LDAP db, but has successfully authenticated with Kerberos!";
		// Add user to LDAP
		global $username, $email, $password, $redirect;
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

	private function get_cn($dn) {
		$eq_pos = strpos($dn, "=") + 1;
		return substr($dn, $eq_pos, strpos($dn, ",") - $eq_pos);
	}

	private function group_of_groups($ldap_handle, $prev_dn, $groups, $level) {
		if ($level > 2) {
			return array();
		}
		$search_result = ldap_search($ldap_handle, $prev_dn, "(objectClass=*)", array('memberOf'));
		$result = ldap_get_entries($ldap_handle, $search_result);
		if ($result['count'] > 0 && isset($result[0]['memberof'])) {
			foreach ($result[0]['memberof'] as $key => $dn) {
				if ($key === 'count') {
					continue;
				}
				if (strpos($dn, 'ou=posix') === false) {
					$groups[] = $dn;
				}
				$groups = array_merge($groups, $this->group_of_groups($ldap_handle, $dn, $groups, $level + 1));
			}
		}
		return $groups;
	}

	/**
	* Search for user in LDAP
	*/
	public function search($search_filter) {
		$ldap_handle = $this->connect();

		$username = LDAP_SEARCH_USER;
		$password = LDAP_SEARCH_PASS;

		ldap_bind($ldap_handle, "cn=$username,{$this->dn}", $password);

		$search_result = ldap_search($ldap_handle, "ou=people,{$this->dn}", $search_filter, array('*', 'memberof'));
		$users = ldap_get_entries($ldap_handle, $search_result);

		if ($users['count'] == 0) {
			ldap_unbind($ldap_handle);
			return false;
		}
		$user = $users[0];

		$groups = array();
		if ($user['memberof']['count'] > 0) {
			foreach ($user['memberof'] as $key => $dn) {
				if ($key === 'count') {
					continue;
				}
				if (strpos($dn, 'ou=posix') === false) {
					$groups[] = $dn;
				}
				$groups = $this->group_of_groups($ldap_handle, $dn, $groups, 0);
			}
		}
		$groups = array_values(array_map(array($this, "get_cn"), array_unique($groups)));

		ldap_unbind($ldap_handle);

		$result[] = array(
			"cid" => $user["uid"][0],
			"dn" => $user["dn"],
			"firstname" => $user["givenname"][0],
			"lastname" => $user["sn"][0],
			"mail" => $user["mail"][0],
			"nick" => $user["nickname"][0],
			"uidnumber" => $user["uidnumber"][0],
			"groups" => $groups,
	        "admissionYear" => $user["admissionyear"][0],
	        "acceptedUserAgreement" => $user["accepteduseragreement"][0] == "TRUE"
		);

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
	public function addUser($userdata) { // $email, $nick, $new_passwd
		$ldap_handle = $this->connect();

		$username = LDAP_ADMIN_USER;
		$password = LDAP_ADMIN_PASS;

		ldap_bind($ldap_handle, "cn=$username,dc=chalmers,dc=it", $password);

		$base_dn = "ou=it,ou=people,{$this->dn}";

		$sr = ldap_search($ldap_handle, $base_dn, "uidnumber=*", array("uidnumber"));

		ldap_sort($ldap_handle, $sr, '-uidnumber');

		$users = ldap_get_entries($ldap_handle, $sr);

		# FIXME: RACE CONDITIONS! 
		$max = $users[0]["uidnumber"][0];

		$ldap_data = $this->userArray($userdata, $max+1);
		$dn = "uid=$this->user,$base_dn";

		ldap_add($ldap_handle, $dn, $ldap_data);

		$error = ldap_error($ldap_handle);
		ldap_unbind($ldap_handle);
		return $error === 0;
	}

	/**
	* Construct an array with user details
	*/
	private function userArray($userdata, $uid) {
		if ($this->chalmers_data == NULL) {
			throw new Exception("Must call 'authChalmers' before");
		}
		$user = array();
		$user["uid"] = $this->user; // cid
		$user["cn"] = $this->chalmers_data[0]["cn"][0]; // firstname lastname
		$user["sn"] = $this->chalmers_data[0]["sn"][0]; // lastname
		$user["givenname"] = $this->chalmers_data[0]["givenname"][0]; // firstname
		$user["mail"] = $userdata["email"];
		$user["nickname"] = $userdata["nick"];
		$user["admissionYear"] = $userdata["admission_year"];
		$user["acceptedUserAgreement"] = $userdata["accept_terms"];
		if ($userdata["nollan_photo"] != null) {
			$user["nollanPhoto"] = file_get_contents($userdata["nollan_photo"]["image"]);
		}

		$user["objectClass"] = array("posixAccount", "chalmersstudent");
		$user["homeDirectory"] = "/home/chalmersit/$this->user";
		$user["loginShell"] = "/bin/bash";
		$user["userPassword"] = $this->generatePassword($userdata["pass"]);
		$user["uidNumber"] = $uid;
		$user["gidNumber"] = 4500;

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

		ldap_bind($ldap_handle, "cn=$username,dc=chalmers,dc=it", $password);

		$replace = array('userpassword' => $this->generatePassword($newPassword));
		$result = ldap_modify($ldap_handle, 'uid='.$this->user.',ou=it,ou=people,dc=chalmers,dc=it', $replace);

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
			if(!$it) {
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
