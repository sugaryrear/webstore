<?php
    if (count(get_included_files()) <= 1) {
        exit;
    }
?>

<div class="well">
    <h3>Hello, <?php echo isset($_COOKIE['store_user']) ? formatName($_COOKIE['store_user']) : "Guest"; ?></h3>
    <hr>
</div>
