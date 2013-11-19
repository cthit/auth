<?php
function askChalmers($cid) {
	// Ask Kerberos about the lost souls roaming the Underworld.
	//TODO: Batch in old users in some way, register?
	$ds = ldap_connect("ldap://ldap.chalmers.se");
	if ($ds) {
		$filter = "(uid=$cid)";
		$search_result = ldap_search($ds, "ou=people,dc=chalmers,dc=se", $filter);
		$result = ldap_get_entries($ds, $search_result);

		ldap_close($ds);
		if ($result["count"] === 0) {
			echo "No result\n";
			return;
		}
		foreach ($result as $key => $r) {
			if ($key === "count") {
				continue;
			}
			echo sprintf("%s, %s\n", $r["cn"][0], $r["mail"][0]);
		}
	}
}
$input_cid = isset($_GET["cid"]) ? $_GET["cid"] : $_POST["cid"];
askChalmers($input_cid);
