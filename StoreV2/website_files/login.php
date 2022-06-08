<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}

	$loggedIn = false;
	$username = null;

	if (isset($_GET['action']) && $_GET['action'] == "logout") {
		setcookie("store_user", "", time() - 1000);
		header("location:index.php");
		exit;
	}

	if (isset($_COOKIE['store_user'])) {
		$loggedIn = true;
		$username = formatName(cleanString($_COOKIE['store_user']));
	} else {
		if (isset($_POST['username'])) {
			$username = cleanString($_POST['username']);
			setcookie("store_user", $username);
			header("location:index.php");
			exit;
		}
	}
?>
