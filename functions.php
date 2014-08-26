<?php
require "config.php";
global $user, $digit;

function is_signed_in() {
	global $user;
	if (!isset($user)) {
		sign_in();
	}
	return isset($user["cid"]);
}

function sign_in() {
	global $user, $digit;
	if (!isset($_COOKIE["chalmersItAuth"])) {
		return;
	}
	$token = $_COOKIE["chalmersItAuth"];
	$user_data = file_get_contents(DOMAIN . "/auth/userInfo.php?token=" . $token);
	$user = json_decode($user_data, true);
	$digit = isset($user, $user["groups"]) && in_array("digit", $user["groups"]);
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

function form_control($id, $text, $type = "text", $icon, $focus) { ?>
	<div class="form-group">
		<label for="<?= $id ?>" class="col-lg-2 control-label"><?= $text ?></label>
		<div class="col-lg-10">
			<div class="input-group">
				<?php if (isset($icon)):?><span class="input-group-addon"><span class="glyphicon glyphicon-<?= $icon ?>"></span></span><?php endif; ?>
				<input id="<?= $id ?>" <?= ((isset($focus) && $focus)?"autofocus":"") ?> name="<?= $id ?>" class="form-control" type="<?=$type?>" placeholder="<?= $text ?>"/>
			</div>
		</div>
	</div>
<?php }

function search_image($cid) {
	$it = new RecursiveDirectoryIterator("nollan-photos");
	$it = new RecursiveIteratorIterator($it);
	$it->rewind();

	while($it->valid()) {
		if (!$it->isDot()) {
			$path = $it->getSubPathName();
			if (strpos($path, "/$cid.jpg")) {
				return array("image" => "nollan-photos/$path", "folder" => $it->getSubPath());
			}
		}
		$it->next();
	}
	return null;
}
