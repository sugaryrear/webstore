<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<div class="panel panel-default">
	<div class="panel-body">
		<h4 style="margin:0;">Hello, <?php echo $username; ?>!</h4>
		
		<form action="process.php" method="post" id="pp_form">
			<?php 
				for ($i = 0; $i < count($pp_config); $i++) {
					echo '<input type="hidden" name="'.$pp_keys[$i].'" value="'.$pp_config[$pp_keys[$i]].'">';
				}
			?>
			
			<input type="hidden" name="custom" value="<?php echo $username; ?>">
			
			<ul class="list-group" id="products">
				<?php
					$total = 0;
					for ($i = 1; $i < 11; $i++) {
						if (isset($_COOKIE['item_'.$i]) && !empty($_COOKIE['item_'.$i])) {
							$data = explode(",", $_COOKIE['item_'.$i]);
							
							if (count($data) == 5) {
								$name = cleanString($data[0]);
								$id = cleanInt($data[1]);
								$amount = cleanInt($data[2]);
								$quantity = cleanInt($data[3]);
								$disc = cleanInt($data[4]);

								echo '
									<li class="cart-item list-group-item">
										<span class="pull-right">$'.(($data[2] - $data[4]) * $data[3]).'</span>
										'.$data[0].' x'.$data[3].'
										<input type="hidden" class="form-control" name="item_number_'.$i.'" value="'.$data[1].'">
										<input type="hidden" class="form-control" name="item_name_'.$i.'" value="'.$data[0].'">
										<input type="hidden" class="form-control" name="amount_'.$i.'" value="'.$data[2].'">
										<input type="hidden" class="form-control" name="quantity_'.$i.'" value="'.$data[3].'">
										<input type="hidden" class="form-control" name="discount_amount_'.$i.'" value="'.($data[4] * $data[3]).'">
									</li>';

								$total += ($data[2] - $data[4]) * $data[3];
							}
						}
					}
				?>
			</ul>
			
			<p class="text-right">Total: $<span id="total"><?php echo number_format($total, 2); ?></span></p>
			
			<div class="form-group">
				<button type="submit" class="btn btn-success btn-block">Checkout with Paypal</button>
			</div>
			
			<div class="form-group text-center">
				<a href="?action=logout">Change Name</a> | 
				<a href="#" id="clear">Empty Cart</a>
			</div>
		</form>
		
	</div>
</div>
