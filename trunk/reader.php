<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	require_once('libs/PEAR/XML_RSS/RSS.php');         // enables simple RSS reading
	require_once('libs/Aidan_Lister/Duration.php');    // writes human readable time durations
	require_once('libs/SimpleHEAD.php');               // passes simple HTTP HEAD requests
	require_once('libs/TempStore.php');                // abstracts data storage
	require_once('libs/ServerTestPackage.php');        // tests aspects of external servers

  // returns MySQL valid timestamp
  function mysql_time($t) {
    return date('Y-m-d H:i:s', $t);
  }

	// prints out fetched key=>val pairs
	function printarr($arr) {
		foreach($arr as $k => $v) {
			if (is_array($v)) {
				printarr($v);
			} else {
				echo("<tr>\n\t<td>&laquo;$k&raquo;</td>\n\t<td>&laquo;".htmlspecialchars($v, ENT_COMPAT, 'UTF-8')."&raquo;</td>\n</tr>\n");
			}
		}
	}	

  // CONSTANTS AND HANDLES
	$shortwait = 30; // time in seconds between each check for new content on well reporting servers (10*60 is a good value here)
	$longwait = 60*60; // time in seconds between each redownload of content on servers not reporting content information (30*60 is a good value here)
	$sql =& new DBXStore('localhost', 'root', '', 'uyo_development'); // data storage handle
	$feeds = $sql->read('feeds', array('id', 'url', 'timer', 'lastmodified', 'etag', 'reports')); // cache of all relevant information from data storage
  $ett = null; // I have to get the ETag we got from the server to the data writing bit somehow...
  $lmm = null; // I have to get the Last-Modified timestamp we got from the server to the data writing bit somehow...
  $update = false; // This also needs a default value to avoid notices--I don't want ANY errors. ;)
  
  //________________________________________________________________
  //================================================================
  //
  //      MAIN LOOP
  //________________________________________________________________
  //================================================================
	foreach($feeds->data as $feed) {
    // Check to see if it's time to update this feed
    $checkhead = false;
    $ett = null;  // clear out old ETag availability value
    $lmm = null;  // clear out old Last-Modified availability value
    
    if ($feed['reports'] == 'complete' || $feed['reports'] == 'etagonly' || $feed['reports'] == 'lmonly') {
      // Server reports some usable information, we can make HEAD requests after a short wait time
      if ($feed['timer'] == 0 || empty($feed['timer']) || $feed['timer'] == null || strtotime($feed['timer'])+$shortwait < time()) {
        // Time since last update longer than required short wait time, or not set timestamp
        $checkhead = true;
      }
		} else {
      // Either reports 'none' (bad server!) or not tested, only check after long wait time has passed
      if ($feed['timer'] == 0 || empty($feed['timer']) || $feed['timer'] == null || strtotime($feed['timer'])+$longwait < time()) {
        // Time since last update longer than required long wait time, or not set timestamp
        $checkhead = true;
      }
    }
    
    if ($checkhead == true) {
      // about time to make a new HEAD request
      if ($feed['reports'] == 'complete' || $feed['reports'] == 'etagonly') {
        // etag checking here
        $resp = SimpleHEAD::HEADrequest($feed['url']);
        if (!isset($feed['etag']) || empty($feed['etag']) || $feed['etag'] == null || !isset($resp['et']) || empty($resp['et']) || $resp['et'] == null) {
          echo "Got no ETag to compare with; proceeding with download.\n";
          $update = true;
        } elseif (isset($feed['etag']) && isset($resp['et']) && !empty($resp['et']) && $resp['et'] != null && $feed['etag']==$resp['et']) {
          echo "Cool, these ETags are equal! No need to update.\n";
          $update = false;
          // we can't check ALL the time!
          $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));
          $ett = $resp['et']; // I have to get this to the data writing bit somehow...
        } elseif (isset($feed['etag']) && isset($resp['et']) && !empty($resp['et']) && $feed['etag'] != null && $feed['etag']!=$resp['et']) {
          echo "These ETags are different; proceeding with download.\n";
          echo $feed['etag'].'   -   '.$resp['et']."\n";
          $update = true;
          $ett = $resp['et']; // I have to get this to the data writing bit somehow...
        } else {
          // Something I failed to think about?
          die("This sure is an interesting error. (Check the ETag code of ".__FILE__." at ".__LINE__.".\n");
        }
        if ($feed['reports'] == 'complete') {
          $lmm = $resp['lm']; // don't forget to store this if we have it
        }
      } elseif ($feed['reports'] == 'lmonly') {
        // lm checking here
        $resp = SimpleHEAD::HEADrequest($feed['url']);
        if (isset($resp['lm']) && !empty($resp['lm'])) {
          $lmm = strtotime($resp['lm']);
        }
        if (!isset($feed['lastmodified']) || strtotime($feed['lastmodified']) == 0 || !isset($resp['lm']) || empty($resp['lm'])) {
          echo "Got no Last-Modified timestamp to compare with; proceeding with download.\n";
          $update = true;
        } elseif (isset($feed['lastmodified']) && $lmm != 0 && strtotime($feed['lastmodified'])==$lmm) {
          echo "Cool, these Last-Modified timestamps are equal! No need to update.\n";
          $update = false;
          // we can't check ALL the time!
          $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));
        } elseif (isset($feed['lastmodified']) && $lmm != 0 && strtotime($feed['lastmodified'])!=$lmm) {
          echo "These timestamps are different; proceeding with download.\n";
          echo strtotime($feed['lastmodified']).'   -   '.$lmm."\n";
          $update = true;
        } else {
          // Something I failed to think about?
          die("This sure is an interesting error. (Check the Last-Modified code of ".__FILE__." at ".__LINE__.".\n");
        }
      } elseif ($feed['reports'] == 'none' || $feed['reports'] == null) {
        if ($feed['reports'] == null) {
          ServerTestPackage::ServerReportingTest($feed['url'], $feed['id'], $sql);
        }
        $ett = null;  // reset these before writing
        $lmm = null;  // reset these before writing
        // no way to check if anything's new... we'll just have to download the whole thing
        $update = true;
      } else {
        // ambiguous database value for 'reports'
        die("Ambiguous database value for 'reports': feed['reports'] = $feed[reports].\n");
      }
    } else { // from if($checkhead)
      // too soon to update, we'll just give out a little notice
      if ($feed['reports'] == 'complete' || $feed['reports'] == 'etagonly' || $feed['reports'] == 'lmonly') {
        // short wait time
        echo "LOL! Too soon d00d.\n".Duration::toString(time()-strtotime($feed['timer']))." since last update. You just gotta wait ".Duration::toString(strtotime($feed['timer'])+$shortwait-time())." more.\n";
      } else {
        // long wait time
        echo "OMG! Too soon d00d.\n".Duration::toString(time()-strtotime($feed['timer']))." since last update. You just gotta wait ".Duration::toString(strtotime($feed['timer'])+$longwait-time())." more.\n";
      }
      
    } // end if($checkhead)
    
    if($update) {
      $rss =& new XML_RSS($feed['url']);
      $rss->parse();
  
      echo
'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
    <title>Test</title>
  </head>
  <body>
    <table>';
    
      printarr($rss->getChannelInfo());
  
      echo
'   </table>
  </body>
</html>

';

      // we've updated, now write a new timestamp
      if (isset($ett) && !empty($ett) && isset($lmm) && !empty($lmm)) {
        // Got both ETag and Last-Modified from server (bravo!)
        $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));  // write the current time
        $sql->write('feeds', 'etag', $feed['id'], $ett); // write the ETag we got
        $sql->write('feeds', 'lastmodified', $feed['id'], mysql_time($lmm)); // write the Last-Modified timestamp we got
      } elseif (isset($ett) && !empty($ett)) {
        // Only got ETag from server
        $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));  // write the current time
        $sql->write('feeds', 'etag', $feed['id'], $ett); // write the ETag we got
      } elseif (isset($lmm) && !empty($lmm)) {
        // Only got Last-Modified from server
        $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));  // write the current time
        $sql->write('feeds', 'lastmodified', $feed['id'], mysql_time($lmm)); // write the Last-Modified timestamp we got
      } else {
        // Got no content information from server (bad server!)
        $sql->write('feeds', 'timer', $feed['id'], mysql_time(time()));  // just write the current time
      }
    }
    
  } // end main loop
?>