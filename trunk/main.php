<?php

//  require_once('classes.php');  // Main classes and class functions
require_once ('config.php');	// Installation configuration
require_once ('module.php'); // Class to handle modules

Module :: search('modules'); // Activate any modules found

// got this from a UiO site. ideally fysmek1() should be able to fetch only the title, header and text.
$t = '<!DOCTYPE HTML PUBLIC "-//SoftQuad//DTD draft HTML 3.2 + extensions for HoTMetaL PRO 3.0 19960802//EN" "hmpro3.dtd">
<HTML> 
  <HEAD> 
         <TITLE>Kurs i difflikninger</TITLE> 
  



</HEAD> 
 <BODY TEXT="#000000" BGCOLOR="#FFFFFF"> 
  
<H2><a href="index.xml"><img src="kulebanelogoxs.gif" align="LEFT" border="0"></a>Kurs 
  i differential-ligninger:</H2>
         
<P><BR>
  <BR>
  <BR>
</P> 
<P>Vi kj&oslash;rer v&aring;ren 2006 et spesielt opplegg p&aring; kurset i differential-ligninger 
  som arrangeres spesielt for MEF-studenter. Kurset gis i form av forelesninger   hver mandag i Store fysiske auditorium like etter at FYS-MEK/F-forelesningen 
  er slutt, dvs mandager kl 1115-1200. Hvorvidt vi skal ha formelle gruppetimer   i tillegg, f&aring;r vi f&oslash;le oss litt fram til. Vi holder p&aring; i 
  s&aring; mange uker som er n&oslash;dvendig f&aring;r &aring; dekke det aktuelle 
  stoffet (<a href="difflikn.html">angitt tidligere</a>), men vi skal i alle fall 
  v&aelig;re ferdig f&oslash;r p&aring;ske (kanskje med god margin).</P>
<P>Kurset er prim&aelig;rt arrangert for MEF-studenter, men er &aring;pent for 
  alle som m&aring;tte &oslash;nske &aring; v&aelig;re med p&aring; det.</P>
<HR> 
         
<P><FONT SIZE="-2">Siste oppdatering 19. januar 2006 av Arnt Inge Vistnes</FONT> 
</P>
<p>&nbsp;</p>
<p>&nbsp;</p>
<P>&nbsp;</P>
</BODY>
</HTML>';
$r = fysmek1($t);
?>