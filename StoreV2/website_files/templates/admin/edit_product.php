<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}

	$product = $db->getProduct(cleanInt($_GET['edit']));

	if ($product == null) {
		echo '<div class="alert alert-danger">That product could not be found</div>';
	} else {
?>

<div class="panel panel-default">
	<div class="panel-heading">Edit Product</div>
	<div class="panel-body">
		<form action="admin.php" method="post">
			<input type="hidden" class="form-control disabled" name="item_id" value="<?php echo $product['item_id']; ?>" >

			<div class="form-group">
				<label>Item Name</label>
				<input type="text" class="form-control" name="item_name" value="<?php echo $product['item_name']; ?>">
			</div>
			<div class="form-group">
				<label>Category</label>
				<select class="form-control" name="category">
					<?php
						$ccat  = $db->getCategory($product['category']);

						if ($ccat == null) {
							echo '<option value="0">Invalid category</option>';
						} else {
							echo '<option value="'.$ccat['cid'].'">'.$ccat['cat_title'].'</option>';
						}

						$categories = $db->getCategories();

						if ($categories == null) {
							echo '<option value="0">No Categories Available</option>';
						} else {
							foreach ($categories as $cat) {
								if ($ccat['cid'] == $cat['cid'])
									continue;
								echo '<option value="'.$cat['cid'].'">'.$cat['cat_title'].'</option>';
							}
						}
					?>
		        </select>
			</div>
			<div class="form-group">
				<label>Price</label>
				<input type="number" class="form-control" name="item_price" step="0.01" min="0" value="<?php echo $product['item_price']; ?>">
			</div>
			<div class="form-group">
				<label>Discount</label>
				<input type="number" class="form-control" name="item_discount" step="0.01" min="0" value="<?php echo $product['item_discount']; ?>">
			</div>
			<div class="form-group">
				<label>Image URL</label>
				<input type="text" class="form-control" name="image_url" value="<?php echo $product['image_url']; ?>">
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success" name="button" value="editprod"><i class="fa fa-save"></i> Save Changes</button>
			</div>
		</form>
	</div>
</div>

<?php } ?>
