<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<div class="panel panel-default">
	<div class="panel-heading">Add Product</div>
	<div class="panel-body">
		<form action="admin.php" method="post">
			<div class="form-group">
				<label>Item Name</label>
				<input type="text" class="form-control" name="item_name">
			</div>
			<div class="form-group">
				<label>Category</label>
				<select class="form-control" name="category">
					<?php
						$categories = $db->getCategories();
						if ($categories == null) {
							echo '<option value="0">No Categories Available</option>';
						} else {
							foreach ($categories as $cat) {
								echo '<option value="'.$cat['cid'].'">'.$cat['cat_title'].'</option>';
							}
						}
					?>
		        </select>
			</div>
			<div class="form-group">
				<label>Price</label>
				<input type="number" class="form-control" name="item_price">
			</div>
			<div class="form-group">
				<label>Discount</label>
				<input type="number" class="form-control" name="item_discount">
			</div>
			<div class="form-group">
				<label>Image URL</label>
				<input type="text" class="form-control" name="image_url">
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success" name="button" value="addprod"><i class="fa fa-plus"></i> Add Product</button>
			</div>
		</form>
	</div>
</div>