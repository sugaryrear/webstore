<?php
	if (!isset($_GET['player'])) {
		exit;
	}

	include 'constants.php';
	include 'classes/class.database.php';

	$name = cleanString($_GET['player']);
	$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);
	$db->connect();

	$payments = $db->getPayments($name);

	foreach ($payments as $pay) {
		echo ''.$pay['item_name'].' :: '.$pay['item_number'].' :: '.$pay['amount'].' :: '.$pay['quantity'].'';
		$db->setClaimed($pay['id']);
	}
	
?>