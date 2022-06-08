<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<a href="?action=payments" class="btn btn-success btn-xl portal-btn">
	<i class="fa fa-bar-chart-o fa-4x"></i><br><br>
	Payments
</a>

<a href="?action=products" class="btn btn-success btn-xl portal-btn">
	<i class="fa fa-shopping-cart fa-4x"></i><br><br>
	Products
</a>
<a href="?action=addprod" class="btn btn-success btn-xl portal-btn">
	<i class="fa fa-cart-plus fa-4x"></i><br><br>
	Add Product
</a>
<a href="?action=addcat" class="btn btn-success btn-xl portal-btn">
	<i class="fa fa-list fa-4x"></i><br><br>
	Add Category
</a>