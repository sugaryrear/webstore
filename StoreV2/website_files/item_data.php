<?php
	$vars = array("item", "item_name", "item_id", "item_price", "amount", "discount");
	
	foreach ($vars as $var) {
		if (!isset($var)) {
			echo 'invalid';
			exit;
		}
	}
	
	$item = $_POST[$vars[0]];
	$name = $_POST[$vars[1]];
	$id = $_POST[$vars[2]];
	$price = $_POST[$vars[3]];
	$amount = $_POST[$vars[4]];
	$discount = $_POST[$vars[5]];
	
	
	echo '<li class="cart-item list-group-item">';
	echo '<span class="pull-right">$'.(($price - $discount) * $amount).'</span>';
	echo ''.$name.' x'.$amount.'';
	echo '<input type="hidden" class="form-control" name="item_number_'.$item.'" value="'.$id.'">';
	echo '<input type="hidden" class="form-control" name="item_name_'.$item.'" value="'.$name.'">';
	echo '<input type="hidden" class="form-control" name="amount_'.$item.'" value="'.$price.'">';
	echo '<input type="hidden" class="form-control" name="quantity_'.$item.'" value="'.$amount.'">';
	echo '<input type="hidden" class="form-control" name="discount_amount_'.$item.'" value="'.($discount * $amount).'">';
	echo '</li>';
?>
	