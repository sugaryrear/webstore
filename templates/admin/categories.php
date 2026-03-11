<?php
	if (count(get_included_files()) <= 1)
		exit;
	
	$categories = $db->getCategories();
	
	echo '<div class="panel panel-default"><div class="panel-heading">Categories</div><ul class="list-group">';
	
	foreach($categories as $cat) {
		echo '<li class="list-group-item">
				<span class="badge"><a href="?delcat='.$cat['cid'].'"><i class="fa fa-times"></i></a></span>
				<a href="?category='.$cat['cid'].'">'.$cat['cat_title'].'</a>
			</li>';
	}
	
	echo '</ul></div>';
			