<?php
// this script will perform all the magic - viz., adding new entries/groups/etc., editing them, deleting them, and all other dirty work

if (isset($GET['action'])) {
	$action = $GET['action'];
	$id = $GET['id'];
} elseif (isset($POST['action'])) {
	$action = $POST['action'];
	$id = $POST['id'];
} else {
	// no action requested
	die("Error: no action requested.\n");
}
switch($action) {
	case 'add':
		// write post to db
		break;
	case 'edit':
		// open editing window (edit.php) (with original post contents where applicable)
		break;
	case 'del':
		// delete post from db
		break;
	default:
		// report error
		die ("Unknown action.\n");
}
?>
