<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}

	if (!isset($_COOKIE['username']) || !isset($_COOKIE['password'])) {
		clearCookies();
    	header("location: admin_login.php");
    	exit;
    }

    if (isset($_GET['action']) && $_GET['action'] == "logout") {
    	clearCookies();
    	header("location: admin_login.php");
    	exit;
    }

    $setUser = decrypt($_COOKIE['username']);
    $setPass = decrypt($_COOKIE['password']);

    if ($setUser != $config['admin_user'] || $setPass != $config['admin_pass']) {
    	clearCookies();
    	header("location: admin_login.php");
    	exit;
    }

    if ($config['allowed_ip'] != "" && $config['allowed_ip'] != $_SERVER['REMOTE_ADDR']) {
    	clearCookies();
    	header("location: admin_login.php");
    	exit;
    }

    $hash = $db->getHash($_COOKIE['password']);

    if ($hash != null) { // if using an existing hash, say from a stolen "cookie" or known hash, then deny login.
    	clearCookies();
    	header("location: admin_login.php");
    	exit;
    }

    $newHash = encrypt($config['admin_pass']);// generate new hash and store it in the database
    $db->addHash($newHash);
    $_COOKIE['password'] = $newHash; // set new hash in the cookie

    function clearCookies() {
    	setcookie("username", '', time() - 3600);
    	setcookie("password", '', time() - 3600);
    	for($i = 0; $i < 10; $i++) {
    		if (!isset($_COOKIE['item_'.$i]))
    			continue;
    		setcookie("item_".$i, '', time() - 3600);
    	}
    }

?>