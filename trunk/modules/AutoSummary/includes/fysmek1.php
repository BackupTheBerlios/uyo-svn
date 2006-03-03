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

/**
 * Returns a summary of the provided text (provided it matches the fysmek1 template)
 */
function fysmek1($t) {
//	$p = '#<!DOCTYPE HTML PUBLIC "-//SoftQuad//DTD draft HTML 3.2 + extensions for HoTMetaL PRO 3.0 '.
//	'19960802//EN" "hmpro3.dtd">.*<TITLE>(.*)</TITLE>.*(<P>.*</P>).*<HR>.*#';
	$p = '#<TITLE>(.*)</TITLE>.*(<P>.*</P>).*<HR>#';
	preg_match_all($p, $t, $r);	// should give an array with (1. page title, 2. content)
	return $r;
}
?>
