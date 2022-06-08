<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}

	$page = isset($_GET['page']) ? cleanInt($_GET['page']) : 1;

	$start = $page == 1 ? 0 : $page * 50;
?>

<form action="admin.php?action=payments" method="post" style="width:200px;margin-right: 10px;margin-top:6px;" class="pull-right">
	<div class="input-group">
		<input class="form-control input-sm" type="text" name="username" placeholder="Search by Username">
		<span class="input-group-btn">
			<button class="btn btn-success btn-xs" type="submit">Search</button>
		</span>
	</div>
</form>

<div class="panel panel-default">
	<div class="panel-heading">
		<?php
			if (isset($_POST['username'])) {
				echo 'All payments for '.cleanString($_POST['username']).'';
			} else {
				echo 'Payment History. Page '.cleanInt($page).'';
			}
		?>
	</div>
	<table class="table">
	<tr>
		<td>Username</td>
		<td>Item Name</td>
		<td>Status</td>
		<td>Paid</td>
		<td>Quantity</td>
		<td>Currency</td>
		<td class="text-right">Date</td>
	</tr>
	<?php
		if (isset($_POST['username'])) {
			$username = cleanString($_POST['username']);
			$payments = $db->getPayments($username);
		} else {
			$payments = $db->getPayments2($start);
		}

		foreach ($payments as $pay) {

			$date = date("M j, Y - g:i A", strtotime($pay['dateline']));
			echo '
			<tr>
				<td>'.$pay['player_name'].'</td>
				<td>'.$pay['item_name'].'</td>
				<td>'.$pay['status'].'</td>
				<td>$'.number_format($pay['amount'], 2).'</td>
				<td>'.$pay['quantity'].'</td>
				<td>'.$pay['currency'].'</td>
				<td class="text-right">'.$date.'</td>
			</tr>';
		}
	?>
	</table>
</div>



<div class="buttons pull-right">
	<a class="btn btn-success" href="?action=payments&amp;page=<?php echo $page == 1 ? 1 : ($page - 1); ?>">Prev Page</a>
	<a class="btn btn-success" href="?action=payments&amp;page=<?php echo ($page + 1); ?>">Next Page</a>
</div>
