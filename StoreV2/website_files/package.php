<?php
	
	$vars = array("name", "itemid", "price", "discount", "quantity");
	
	foreach ($vars as $var) {
		if (!isset($var)) {
			echo 'invalid';
			exit;
		}
	}

	$data = array(
		"name" => $_POST['name'],
		"id" => $_POST['itemid'],
		"price" => $_POST['price'],
		"quantity" => $_POST['quantity'],
		"discount" => $_POST['discount']
	);

	$itemId = 0;

	for ($i = 1; $i < 11; $i++) {
		if (!isset($_COOKIE['item_'.$i])) {
			$itemId = $i;
			break;
		}
	}

	if (!containsItem($data['id'])) {
		if ($itemId != 0) {
			$itemdata = ''.$data['name'].','.$data['id'].','.$data['price'].','.$data['quantity'].','.$data['discount'].'';
			setcookie('item_'.$itemId, $itemdata);
			header("location: index.php");
			exit;
		}
	}

	
?>