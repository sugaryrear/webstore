<?php
if (count(get_included_files()) <= 1) {
	exit;
}

$title = htmlspecialchars($product['item_name'] ?? 'Unknown item');
$desc = htmlspecialchars($product['description'] ?? '');
$item_id = (int)($product['item_id'] ?? 0);
$image = $product['image_url'] ?? '';
$value = (float)($product['item_price'] ?? $product['price'] ?? 0);
$discount = (float)($product['item_discount'] ?? 0);
?>

<div class="col-md-3 text-center">
	<div class="item">
		<?php if (!empty($image)) { ?>
			<div class="text-center">
				<img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo $title; ?>" style="max-width:100%; max-height:150px;">
			</div>
		<?php } ?>

		<h5><?php echo $title; ?></h5>

		<?php if (!empty($desc)) { ?>
			<p><?php echo $desc; ?></p>
		<?php } ?>

		<div class="item-price">
			<?php
				if ($discount != 0) {
					echo '<span class="text-success">$' . number_format($value - $discount, 2) . '</span>
						  <strike class="text-danger">$' . number_format($value, 2) . '</strike>';
				} else {
					echo '$' . number_format($value, 2);
				}
			?>
		</div>

		<?php
		 	if ($loggedIn) {
				include 'templates/item_form.php';
			}
		?>
	</div>
</div>