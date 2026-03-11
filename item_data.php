<?php
$vars = array("item", "item_name", "item_id", "item_price", "amount", "discount");

foreach ($vars as $var) {
    if (!isset($_POST[$var])) {
        echo 'invalid';
        exit;
    }
}

$item = $_POST["item"];
$name = $_POST["item_name"];
$id = $_POST["item_id"];
$price = $_POST["item_price"];
$amount = $_POST["amount"];
$discount = $_POST["discount"];

echo '<li class="cart-item list-group-item">';
echo '<span class="pull-right">$' . (($price - $discount) * $amount) . '</span>';
echo $name . ' x' . $amount;
echo '<input type="hidden" class="form-control" name="item_number_' . $item . '" value="' . $id . '">';
echo '<input type="hidden" class="form-control" name="item_name_' . $item . '" value="' . $name . '">';
echo '<input type="hidden" class="form-control" name="amount_' . $item . '" value="' . $price . '">';
echo '<input type="hidden" class="form-control" name="quantity_' . $item . '" value="' . $amount . '">';
echo '<input type="hidden" class="form-control" name="discount_amount_' . $item . '" value="' . ($discount * $amount) . '">';
echo '</li>';
?>