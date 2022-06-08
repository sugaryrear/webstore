<?php
	include 'constants.php';
	include 'classes/class.database.php';

	define("DEBUG", 1);
	define("LOG_FILE", "paypal.log");
	define("USE_SANDBOX", 0);

	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();

	foreach ($raw_post_array as $keyval) {
		$keyval = explode ('=', $keyval);
		if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
	}

	$req = 'cmd=_notify-validate';

	if(function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}

	foreach ($myPost as $key => $value) {
		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}

	if(USE_SANDBOX == true) {
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$ch = curl_init($paypal_url);

	if ($ch == FALSE) {
		return FALSE;
	}

	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

	if(DEBUG == true) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
	}

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

	$res = curl_exec($ch);

	if (curl_errno($ch) != 0) // cURL error
		{
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
		}
		curl_close($ch);
		exit;
	} else {
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
		}
		curl_close($ch);
	}

	$tokens = explode("\r\n\r\n", trim($res));
	$res = trim(end($tokens));

	if (strcmp ($res, "VERIFIED") == 0) {
		$status = $_POST['payment_status'];
		$currency = $_POST['mc_currency'];
		$receiver = $_POST['receiver_email'];
		$buyer = $_POST['payer_email'];
		$buyer_name = $_POST['custom'];

 		$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);
		$db->connect();

		for ($i = 0; $i < 10; $i++) {
			if (!isset($_POST['item_name'.$i]))
				continue;

			$item_name = $_POST['item_name'.$i];
			$item_number = $_POST['item_number'.$i];
			$paid = $_POST['mc_gross_'.$i];
			$quantity = $_POST['quantity'.$i];

			$data = array(
				"item_name" => $item_name,
				"item_number" => $item_number,
				"status" => $status,
				"amount" => $paid,
				"quantity" => $quantity,
				"currency" => $currency,
				"buyer" => $buyer,
				"receiver" => $receiver,
				"player_name" => $buyer_name
			);

			$product = $db->getProduct($item_number);

			if ($product == null) {
				$data['status'] = "Invalid";
			} else {
				$price = $product['item_price'];
				$discount = $product['item_discount'];
				$actual = $price - $discount;
				$status = "Completed";

				if (($paid / $quantity) != $actual)
					$data['status'] = "Invalid";
				if ($item_name != $product['item_name'])
					$data['status'] = "Invalid";
			}

			$db->insert("payments", $data);
		}

		if(DEBUG == true)
			error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
	} else if (strcmp ($res, "INVALID") == 0) {
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
		}
	}
?>
