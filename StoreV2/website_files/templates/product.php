<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
	
	$title = $product['item_name'];
	$desc = $product['description'];
	$item_id = $product['item_id'];
	$image = $product['image_url'];
	$value = $product['item_price'];
	$discount = $product['item_discount'];
?>

<div class="col-md-3 text-center">
	<div class="item">
		<div class="text-center"><img src="<?php echo $image; ?>"></div>
		<h5><?php echo $title; ?></h5>
		<div class="item-price">
			<?php
				if ($discount != 0) {
					echo '<span class="text-success">$'.number_format($value-$discount, 2).'</span>
						 <strike class="text-danger">$'.number_format($value, 2).'</strike>';
				} else {
					echo '$'.number_format($value, 2).'';
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