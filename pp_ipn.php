<?php
include 'constants.php';
include 'classes/class.database.php';

define("DEBUG", 1);
define("LOG_FILE", "paypal.log");
define("USE_SANDBOX", 0);

function log_ipn($message) {
    if (DEBUG) {
        error_log(date('[Y-m-d H:i e] ') . $message . PHP_EOL, 3, LOG_FILE);
    }
}

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();

foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

$req = 'cmd=_notify-validate';

$get_magic_quotes_exists = false;
if (function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}

foreach ($myPost as $key => $value) {
    if ($get_magic_quotes_exists && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}

$paypal_url = USE_SANDBOX ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr";

$ch = curl_init($paypal_url);
if ($ch == false) {
    exit;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if (DEBUG) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}

$res = curl_exec($ch);

if (curl_errno($ch) != 0) {
    log_ipn("Can't connect to PayPal to validate IPN message: " . curl_error($ch));
    curl_close($ch);
    exit;
}

if (DEBUG) {
    log_ipn("HTTP request of validation request: " . curl_getinfo($ch, CURLINFO_HEADER_OUT) . " for IPN payload: $req");
    log_ipn("HTTP response of validation request: $res");
}

curl_close($ch);

$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));

if (strcmp($res, "VERIFIED") === 0) {
    $paymentStatus = strtolower(trim($_POST['payment_status'] ?? ''));
    $currency      = trim($_POST['mc_currency'] ?? '');
    $receiver      = trim($_POST['receiver_email'] ?? '');
    $buyer         = trim($_POST['payer_email'] ?? '');
    $username      = strtolower(cleanString($_POST['custom'] ?? ''));
    $txnId         = trim($_POST['txn_id'] ?? '');

    if ($username === '' || $txnId === '') {
        log_ipn("Missing username or txn_id.");
        exit;
    }

    $db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);
    if (!$db->connect()) {
        log_ipn("Database connection failed.");
        exit;
    }

    for ($i = 1; $i <= 10; $i++) {
        if (!isset($_POST['item_name' . $i])) {
            continue;
        }

        $postedItemName = trim($_POST['item_name' . $i]);
        $postedItemId   = cleanInt($_POST['item_number' . $i] ?? 0);
        $postedPaid     = (float)($_POST['mc_gross_' . $i] ?? 0);
        $postedQuantity = cleanInt($_POST['quantity' . $i] ?? 0);

        if ($postedItemId <= 0 || $postedQuantity <= 0) {
            log_ipn("Skipping invalid line item at index $i.");
            continue;
        }

        $product = $db->getProduct($postedItemId);
        if ($product == null) {
            log_ipn("Product not found for item_id=$postedItemId");
            continue;
        }

        $expectedName      = $product['item_name'];
        $expectedUnitPrice = (float)$product['item_price'];
        $actualUnitPaid    = round($postedPaid / $postedQuantity, 2);

        $finalStatus = 'invalid';

        if ($paymentStatus === 'completed'
            && $postedItemName === $expectedName
            && abs($actualUnitPaid - $expectedUnitPrice) < 0.01) {
            $finalStatus = 'completed';
        }

        if (!$db->paymentExists($txnId, $username, $postedItemId)) {
            $db->insertPayment(array(
                'username'          => $username,
                'product_name'      => $expectedName,
                'item_id'           => (int)$product['item_id'],
                'item_quantity'     => $postedQuantity,
                'price'             => $expectedUnitPrice,
                'invoice_id'        => $txnId,
                'purchase_datetime' => date('Y-m-d H:i:s'),
                'claimed_datetime'  => null,
                'ip_address'        => $_SERVER['REMOTE_ADDR'] ?? '',
                'serial_address'    => '',
                'status'            => $finalStatus,
                'store'             => 'Grimoire'
            ));
        }
    }

    log_ipn("Verified IPN processed for username=$username txn_id=$txnId currency=$currency receiver=$receiver buyer=$buyer");
} else if (strcmp($res, "INVALID") === 0) {
    log_ipn("Invalid IPN: $req");
}
?>