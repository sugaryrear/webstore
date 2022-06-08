<?php 
	if (count(get_included_files()) <= 1) {
		exit;
	}
?>

<form action="index.php" method="post" id="itemform">
	<input type="hidden" name="name" value="<?php echo $title; ?>">
	<input type="hidden" name="itemid" value="<?php echo $item_id; ?>">
	<input type="hidden" name="price" value="<?php echo $value; ?>">
	<input type="hidden" name="discount" value="<?php echo $discount; ?>">
	<div class="input-group text-center input-custom" id="amount_input" style="width: 100%">
	    <input type="number" class="form-control input-sm" name="quantity" value="1" min=1 max=100>
	    <span class="input-group-btn">
	      	<button class="btn btn-xs btn-default" data-toggle="tooltip" title="Add" data-placement="right"><i class="fa fa-plus"></i></button>
	    </span>
	</div>
</form>