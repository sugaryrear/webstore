<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<div class="panel panel-default">
	<div class="panel-heading">Add Category</div>
	<div class="panel-body">
		<form action="admin.php" method="post">
			<div class="form-group">
  				<label class="control-label">Category Title</label>
  				<div class="input-group">
				    <input type="text" class="form-control" name="cat_title">
				    <span class="input-group-btn">
				    	<button class="btn btn-success" name="button" value="addcat" type="submit">Save</button>
				    </span>
  				</div>
			</div>
		</form>
	</div>
</div>