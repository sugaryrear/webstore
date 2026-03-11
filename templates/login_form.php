<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>
<div class="panel panel-default">
	<div class="panel-heading">Enter your Username</div>
	<div class="panel-body">
		<form action="index.php" method="post" id="login">
			<div class="form-group">
				<input type="text" name="username" id="username" class="form-control" placeholder="Username">
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success btn-block">Continue</button>
			</div>
		</form>
	</div>
</div>
