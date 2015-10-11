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
		$this->host = 'ldaps://ldap.chalmers.it';
		$this->user = $user;
	}

	private function connect() {
		$ldap_handle = ldap_connect($this->host, 636);

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

		$isSuccess = @ldap_bind($ldap_handle, 'uid='.$this->user.',ou=people,'.$this->dn, $password);
		ldap_unbind($ldap_handle);

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

	/**
	* Search for user in LDAP
	*/
	public function search($search_filter) {
		$ldap_handle = $this->connect();

		$username = LDAP_SEARCH_USER;
		$password = LDAP_SEARCH_PASS;

		$attributes = array('givenname','sn','mail','uidNumber','uid','nickname', 'admissionYear', 'acceptedUserAgreement');
		ldap_bind($ldap_handle, 'cn='.$username.','.$this->dn, $password);

		$search_result = ldap_search($ldap_handle, 'ou=people,'.$this->dn, $search_filter, $attributes);
		$users = ldap_get_entries($ldap_handle, $search_result);

		if ($users["count"] === 0) {
			ldap_unbind($ldap_handle);
			return false;
		}

		$search_result = ldap_search($ldap_handle, 'ou=groups,'.$this->dn, "(cn=*)", array("cn", "member"));
		$groups_entries = ldap_get_entries($ldap_handle, $search_result);

		foreach($users as $user) {
			if(is_array($user)) {
				$user_groups = array();
                $cid = $user["uid"][0];
                $uid = $user["dn"];
				foreach($groups_entries as $group) {
					if (isset($group["member"])) {
                        if ($this->check_group_recursive($ldap_handle, $uid, $group["dn"])) {
                            $user_groups[] = $group["cn"][0];
                        }
					}
				}
				$result[] = array(
				"cid" => $cid,
				"dn" => $uid,
				"firstname" => $user["givenname"][0],
				"lastname" => $user["sn"][0],
				"mail" => $user["mail"][0],
				"nick" => $user["nickname"][0],
				"uidnumber" => $user["uidnumber"][0],
				"groups" => $user_groups,
                "admissionYear" => $user["admissionyear"][0],
                "acceptedUserAgreement" => $user["accepteduseragreement"][0] == "TRUE"
				);
			}
		}
		ldap_unbind($ldap_handle);

		if(sizeof($result) == 1 ) {
			$result = $result[0];
		}

		return $result;
	}

    public function check_group_recursive($ad, $userdn, $groupdn) {
        $attributes = array('memberof');
        $result = ldap_read($ad, $userdn, '(objectclass=*)', $attributes);
        if ($result === false) {
            return false;
        }
        $entries = ldap_get_entries($ad, $result);
        if ($entries['count'] <= 0) {
            return false;
        }
        if (empty($entries[0]['memberof'])) {
            return false;
        } else {
            for ($i = 0; $i < $entries[0]['memberof']['count']; $i++) {
                if ($entries[0]['memberof'][$i] == $groupdn) {
                    return true;
                } elseif ($this->check_group_recursive($ad, $entries[0]['memberof'][$i], $groupdn)) {
                    return true;
                }
            }
        }
        return false;
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
		$username = LDAP_ADMIN_USER;
		$password = LDAP_ADMIN_PASS;

		$ldap_handle = $this->connect();

		ldap_bind($ldap_handle, 'cn=admin,dc=chalmers,dc=it', $password);

		$base_dn = "cn=users," . $this->dn;

		$sr = ldap_search($ldap_handle, $base_dn, "uidnumber=*", array("uidnumber"));

		$users = ldap_get_entries($ldap_handle, $sr);

		# FIXME: RACE CONDITIONS!
		$max = 0;
		foreach($users as $user) {
			if ($user["uidnumber"][0] > $max)
				$max = $user["uidnumber"][0];
		}

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
		$user["displayname"] = $userdata["nick"];
		$user["nickname"] = $userdata["nick"];
		$user["admissionYear"] = $userdata["admission_year"];
		$user["acceptedUserAgreement"] = $userdata["accept_terms"];
		if ($userdata["nollan_photo"] != null) {
			$user["nollanPhoto"] = file_get_contents($userdata["nollan_photo"]["image"]);
		}

		$user["objectClass"] = array("inetOrgPerson", "posixAccount", "top", "chalmersstudent");
		$user["homeDirectory"] = "/home/chalmersit/$this->user";
		$user["loginShell"] = "/bin/bash";
		$user["userPassword"] = $this->generatePassword($userdata["pass"]);
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

	public function getGroups() {
	}
}
