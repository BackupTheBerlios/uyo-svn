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

// just to avoid a notice
$entry = array();

// this script will display the editor window
if (isset($POST['id']) || isset($GET['id'])) {
	// fetch old post contents
	if (isset($POST['id'])) {
		$id = $POST['id'];
	} else {
		$id = $GET['id'];
	}
	$sql = & new DBXStore('localhost', 'root', '', 'uyo_development'); // data storage handle
	// read old post contents from data storage
	$entry = $sql->read('entries', array ('id', 'author', 'group', 'title', 'text', 'status', 'type'), "`id`='$id'");
}

// extremely plain, botched together interface
echo '<html>
	<head>
		<title>';
if (isset($id)) {
	echo "Editing post &laquo;$entry[title]&raquo;";
} else {
	echo 'New post';
}
echo '</title>
	</head>
	<body>
		<form method="POST"'./*Or perhaps it should be GET?*/' action="magic.php">
			<h1>';
if (isset($id)) {
	echo "Editing post &laquo;$entry[title]&raquo;";
} else {
	echo 'New post';
}
echo '</h1>
			<table>
				<tr>
					<td>
						Title:
					</td>
					<td>
						<input type="text" name="title" value="'.$entry['title'].'" size="20" />
					</td>
				</tr>
				<tr>
					<td>
						Text:
					</td>
					<td>
						<textarea cols="20" rows="8" name="text">'.$entry['text'].'</textarea>
					</td>
				</tr>
			</table>
			<input type="submit" name="store" value=" Store " />
			<input type="hidden" name="status" value="manual" />
			<select name="group">';
			// an interator here
			echo '
			</select>
		</form>
	</body>
</html>
';
?>
