<?php
	session_start();
	include 'constants.php';
	include 'classes/class.database.php';

	$db = new Database($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database']);

    if (!$db->connect())
    	exit;

    $error = null;

    if (isset($_POST['username']) && isset($_POST['password'])) {
    	$username = encrypt($_POST['username']);
    	$password = encrypt($_POST['password']);

    	if (decrypt($username) != $config['admin_user'] || decrypt($password) != $config['admin_pass']) {
			$error = "Invalid Credentials";
    	} else {
    		setcookie("username", $username);
    		setcookie("password", $password);
    		header("location: admin.php");
    		exit;
    	}

    }
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
	        	<div class="panel-heading">Login</div>
	        	<div class="panel-body">
	        		<div class="text-center text-danger">
	        		<?php
	        			if ($error != null)
	        				echo $error.'<hr>';
	        		?>
	        		</div>
		        	<form action="admin_login.php" method="post">
			        	<div class="form-group">
			        		<input type="text" name="username" class="form-control input-sm" placeholder="Username">
			        	</div>
			        	<div class="form-group">
			        		<input type="password" name="password" class="form-control input-sm" placeholder="Password">
			        	</div>
			        	<div class="form-group">
			        		<button type="submit" class="btn btn-success btn-block">Sign In</button>
			        	</div>
		        	</form>
	        	</div>
        	</div>
        </div>

        <div class="col-xs-9 server-list">

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
