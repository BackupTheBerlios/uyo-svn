<?php
/**
 * Returns a summary of the provided text (provided it matches the fysmek1 template)
 */
function fysmek1($t) {
	$p = '^<!DOCTYPE HTML PUBLIC "-//SoftQuad//DTD draft HTML 3.2 + extensions for HoTMetaL PRO 3.0 '.
	'19960802//EN" "hmpro3.dtd">\s<HTML>\s<HEAD>\s<TITLE>(.*)</TITLE>\s</HEAD>\s<BODY TEXT="#[0-9a-fA-F]{6}'.
	'" BGCOLOR="#[0-9a-fA-F]{6}">\s<H2>.*kulebanelogoxs.gif.*>(.*)</H2>\s(<P>(<BR>\s)+</P>\s)?<P>(.*)</P>\s<HR>^';
	preg_match_all($p, $t, $r);	// should give an array with (1. page title, 2. page header, 3. content)
	return $r;
}
?>
