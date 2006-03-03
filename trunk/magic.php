<?php
/*
 * This file is part of UyO.no.
 * 
 * UyO.no is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * UyO.no is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with UyO.no; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

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
		// update post in db
		break;
	case 'del':
		// delete post from db
		break;
	default:
		// report error
		die ("Unknown action.\n");
}
?>
