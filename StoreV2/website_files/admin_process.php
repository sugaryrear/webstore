<?php
	if (count(get_included_files()) <= 1) {
		exit;
	}

	if (isset($_POST['button'])) {
    	$button = $_POST['button'];

    	if ($button == "addprod") {
    		$vars = array("item_name", "category", "item_price", "item_discount", "image_url");
    		if (!postIsSet($vars) || count($_POST) != count($vars) + 1) {
 				$error = "Please fill in all of the fields correctly.";
    		} else {
    			unset($_POST['button']);
    			$db->insert("products", array(
    				"item_name" => cleanString($_POST['item_name']),
    				"category" => cleanInt($_POST['category']),
    				"item_price" => $_POST['item_price'],
    				"item_discount" => $_POST['item_discount'],
    				"image_url" => $_POST['image_url'],
    			));
    			header("location:admin.php?action=products");
    			exit;
    		}
    	} else if ($button == "addcat") {
    		$vars = array("cat_title");
    		if (!postIsSet($vars) || count($_POST) != count($vars) + 1) {
 				$error = "Please fill in all of the fields correctly.";
    		} else {
    			unset($_POST['button']);
    			$db->insert("categories", array(
    				"cat_title" => cleanString($_POST['cat_title'])
    			));
    			header("location:admin.php");
    			exit;
    		}
    	} else if ($button == "editprod") {
    		$vars = array("item_id", "item_name", "category", "item_price", "item_discount", "image_url");
    		if (!postIsSet($vars) || count($_POST) != count($vars) + 1) {
 				$error = "Please fill in all of the fields correctly.";
    		} else {
    			$itemId = cleanInt($_POST['item_id']);
                $product = $db->getProduct($itemId);

                if ($product == null) {
                    $error = "That product does not exist and therefore can not be edited.";
                } else {
                    unset($_POST['button']);
                    unset($_POST['item_id']);

                    $db->update("products", $itemId, $_POST);

                    header("location:admin.php?action=products");
                    exit;
                }
    		}
    	}

    }

    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    	$delete = cleanInt($_GET['delete']);

        $product = $db->getProduct($delete);

        if ($product == null) {
            $error = "That product does not exist and therefore can not be deleted.";
        } else {
            $db->deleteProduct($delete);
            header("location:admin.php?action=products");
            exit;
        }
    }

    if (isset($_GET['delcat']) && is_numeric($_GET['delcat'])) {
    	$delete = cleanInt($_GET['delcat']);
    	$db->deleteCategory($delete);
    	header("location:admin.php");
    	exit;
    }

?>