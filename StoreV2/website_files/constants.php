<?php
if (count(get_included_files()) <= 1) {
	exit;
}

$config = array( // database info and ACP info
	"db_host" => "",
	"db_user" => "",
	"db_pass" => "",
	"db_database" => "",

	"admin_user" => "admin", // admin user
	"admin_pass" => "admin", // admin pass

	"allowed_ip" => "" // optional, for added security. leave blank to not use
);

$pp_config = array( // edit this stuff where necessary. Email, and the links.
	'business' 			=> "",
	'no_note' 			=> 1,
	'cmd'				=> "_cart",
	'upload'			=> 1,
	'address_override' 	=> 1,
	'return' 			=> "/store/index.php",
	'cancel_return' 	=> "/store/",
	'notify_url' 		=> "/store/pp_ipn.php",
	'cpp_header_image' 	=> "/store/assets/img/logo.png"
);

$pp_keys = array_keys($pp_config);
$dir = count(explode("/", $_SERVER['PHP_SELF'])) > 3 ? "../" : "";

function formatName($string) {
	return cleanString(ucwords(str_replace("_", " ", $string)));
}

function cleanString($string) {
	return filter_var(preg_replace("/[^A-Za-z0-9_ ]/", '', $string), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
}

function cleanEmail($string) {
	return preg_replace("/[^a-zA-Z0-9.!#$%&@'*+-]/", "", $string);
}

function cleanUrl($string) {
	return preg_replace("/[^a-zA-Z0-9.+-_/]/", "%20", $string);
}

function cleanInt($string) {
	return preg_replace("/[^0-9.]/", ' ', $string);
}

function containsItem($id) {
	for ($i = 1; $i < 11; $i++) {
		if (isset($_COOKIE['item_'.$i]) && !empty($_COOKIE['item_'.$i])) {
			$item = explode(",", $_COOKIE['item_'.$i]);
			if ($item[1] == $id) {
				return true;
			}
		}
	}
	return false;
}

function postIsSet($vars) {
	foreach ($vars as $var) {
		if (!isset($_POST[$var]))
			return false;
	}
	return true;
}

function encrypt($data) {
	$size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($size, MCRYPT_RAND);
	$crypt = mcrypt_encrypt(MCRYPT_BLOWFISH, '4HBJ=^@w6p_kcQex', $data, MCRYPT_MODE_CBC, $iv);
	return rtrim(bin2hex($iv . $crypt));
}

function decrypt($data) {
	$iv = pack("H*", substr($data, 0, 16));
	$x = pack("H*", substr($data, 16));
	$res = mcrypt_decrypt(MCRYPT_BLOWFISH, '4HBJ=^@w6p_kcQex', $x, MCRYPT_MODE_CBC, $iv);
	return rtrim($res);
}
?>
