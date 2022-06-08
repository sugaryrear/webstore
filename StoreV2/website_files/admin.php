<?php
	session_start();
	include 'constants.php';
	include 'classes/class.database.php';

	$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);

    if (!$db->connect())
    	exit;

    include 'user.php';
    $error = null;

    include 'admin_process.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'templates/head.php'; ?>
<body>
	
	<div class="nav-wrapper">
		<div class="container">
			<?php include 'templates/page_header.php'; ?>
		</div>
	</div>

	<div class="container wrapper">

		<div class="col-xs-3 server-list">
			<div class="panel panel-default">
	        	<div class="panel-body text-center">
	        		Hello,<br>
	        		<h3 class="no-margin"><?php echo formatName(decrypt($_COOKIE['username'])); ?></h3><br>
		        	<a href="?action=logout">Sign Out</a>
	        	</div>
        	</div>

        	<?php include 'templates/admin/categories.php'; ?>
        </div>

        <div class="col-xs-9 server-list">
        	<?php
        		if ($error != null) {
        			echo '<div class="alert alert-danger">'.$error.'</div>';
        		}
        	?>

        	<?php if (isset($_GET['action']) || isset($_GET['edit'])) { ?>
        	<div>
        		<a href="admin.php" class="btn btn-success btn-sm"><i class="fa fa-arrow-left"></i> Go Back</a>
				<br><br>
			</div>
			<?php } ?>

        	<?php
        		if (isset($_GET['action'])) {
        			$action = cleanString($_GET['action']);
        			if ($action == "addcat") {
        				include 'templates/admin/add_category.php';
        			} else if ($action == "addprod") {
        				include 'templates/admin/add_product.php';
        			} else if ($action == "payments") {
        				include 'templates/admin/payments.php';
        			} else if ($action == "products") {
        				include 'templates/admin/product_list.php';
        			}
        		} else if (isset($_GET['edit'])) {
					include 'templates/admin/edit_product.php';
        		} else {
        			include 'templates/admin/portal.php';
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
