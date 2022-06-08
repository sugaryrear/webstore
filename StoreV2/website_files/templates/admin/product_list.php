<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<div class="panel panel-default">
	<table class="table">
	<tr>
		<td>Id</td>
		<td>Image</td>
		<td>Name</td>
		<td>Category</td>
		<td>Price</td>
		<td>Discount</td>
	</tr>
	<?php
			$products = $db->getAllProducts();
			foreach ($products as $product) {
				$category = $db->getCategory($product['category']);
				echo '
				<tr style="vertical-align:middle;">
					<td>'.$product['item_id'].'</td>
					<td><img src="'.$product['image_url'].'" width=20></td>
					<td>'.$product['item_name'].'</td>
					<td>'.$category['cat_title'].'</td>
					<td>$'.number_format($product['item_price'], 2).'</td>
					<td>$'.number_format($product['item_discount'], 2).'</td>
					<td class="text-right">
						<a href="admin.php?edit='.$product['item_id'].'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>
						<a href="admin.php?delete='.$product['item_id'].'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Delete"><i class="fa fa-times fa-fw"></i></a>
					</td>
				</tr>';
			}
	?>
	</table>
</div>