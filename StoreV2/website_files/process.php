<?php
	include 'constants.php';
	include 'classes/class.database.php';

	if (!isset($_COOKIE['store_user'])) {
		header("location:index.php");
		exit;
	}

	$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);
	$db->connect();

	$get_keys = array_keys($_POST);
	$pp_keys = array_keys($pp_config);
	$invalid = false;

	// paypal info validation
	for ($i = 0; $i < count($pp_config); $i++) {
		if ($_POST[$get_keys[$i]] != $pp_config[$pp_keys[$i]]) {
			header("location: index.php");
			exit;
		}
	}

	// package validation
	for ($i = 1; $i < 11; $i++) {
		if (!isset($_POST['item_number_'.$i])) {
			continue;
		}

		$itemData = $db->getProduct(cleanInt($_POST['item_number_'.$i]));

		if ($itemData == null) {
			$invalid = true;
			break;
		}

		if (!is_numeric($_POST['item_number_'.$i]) || $_POST['item_number_'.$i] != $itemData['item_id']) {
			$invalid = true;
		}
		if (!is_string($_POST['item_name_'.$i]) || $_POST['item_name_'.$i] != $itemData['item_name']) {
			$invalid = true;
		}
		if (!is_numeric($_POST['quantity_'.$i]) || $_POST['quantity_'.$i] < 0) {
			$invalid = true;
		}
		if ($_POST['amount_'.$i] != $itemData['item_price']) {
			$invalid = true;
		}
	}

	// if purchase is invalid (ie. modified form data, then exit)
	if ($invalid) {
		echo 'Invalid form data detected! Please go back and make a proper purchase!';
		exit;
	}

	//default necessary data
	$data = array('custom' => cleanString($_POST['custom']));

	// add paypal info
	for ($i = 0; $i < count($pp_config); $i++) {
		$data[$pp_keys[$i]] = $pp_config[$pp_keys[$i]];
	}

	// add package info
	for ($i = 1; $i < 11; $i++) {
		if (!isset($_POST['item_number_'.$i])) {
			continue;
		}
		$data['item_number_'.$i] = $_POST['item_number_'.$i];
		$data['item_name_'.$i] = $_POST['item_name_'.$i];
		$data['amount_'.$i] = $_POST['amount_'.$i];
		$data['quantity_'.$i] = $_POST['quantity_'.$i];
		$data['discount_amount_'.$i] = $_POST['discount_amount_'.$i];
	}

	$location = "https://www.paypal.com/cgi-bin/webscr?".http_build_query($data, '', '&');
	header("refresh:1; url=$location");
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'templates/head.php'; ?>
<body>
	<div class="container text-center" style="width:750px;padding-top:50px;padding-bottom:50px;margin-top: 150px;">
		<h1><i class="fa fa-paypal"></i></h1>
		<p>Thank you, <?php echo cleanString($_COOKIE['store_user']); ?>
		<h1>Proceeding to Paypal</h1>
		<p>This will only take a moment..</p>
	</div>
</body>
<script type="text/javascript" src="assets/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/js/jquery.cookie.js"></script>
<script type="text/javascript" src="assets/js/custom.js"></script>
<script type="text/javascript" src="assets/js/sweetalert.min.js"></script>
</html>
