<?php
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
