<?php
	include 'constants.php';
	include 'classes/class.database.php';

	$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);

    if (!$db->connect())
    	exit;

    include 'login.php';

	if (isset($_POST['itemid'])) {
		include 'package.php';
	}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'templates/head.php'; ?>
<body>


		<div class="container">
			<?php include 'templates/page_header.php'; ?>
		</div>

	<div class="container wrapper">

		<div class="col-lg-3 col-xs-12 server-list">
        	<?php
				if (!$loggedIn) {
					include 'templates/login_form.php';
				} else {
					include 'templates/user_panel.php';
				}

				$categories = $db->getCategories();

				echo '<div class="panel panel-default"><div class="panel-heading">Categories</div><ul class="list-group">';

				foreach($categories as $cat) {
					echo '<li class="list-group-item">
							<span class="badge">'.$db->countProductsInCat($cat['cid']).'</span>
							<a href="?category='.$cat['cid'].'">'.$cat['cat_title'].'</a>
						</li>';
				}

				echo '</ul></div>';
			?>
        </div>

        <div class="col-lg-9 col-xs-12 server-list">
        	<?php
				$products = null;
				if (isset($_GET['category'])) {
					$products = $db->getProducts(cleanInt($_GET['category']));
				} else {
					$products = $db->getAllProducts();
				}

				for ($i = 0; $i < count($products); $i++) {
					$product = $products[$i];
					include 'templates/product.php';
				}
			?>
        </div>



    </div>

    <?php include 'templates/footer.php'; ?>
</body>
<script type="text/javascript" src="assets/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/js/jquery.cookie.js"></script>
<script type="text/javascript" src="assets/js/custom.js"></script>
<script type="text/javascript" src="assets/js/sweetalert.min.js"></script>
</html>
