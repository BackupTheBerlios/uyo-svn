<?php
	require_once('libs/SimpleHEAD.php');               // passes simple HTTP HEAD requests
  require_once('libs/Aidan_Lister/Duration.php');    // writes human readable time durations
  
  class ServerTestPackage {
    function ServerTestPackage() {
    }
    
    // a test to see if the server reports Last-Modified and/or ETag values for this URL
    // params: URL to test, ID of TempStore field, TempStore handle
    function ServerReportingTest($url, $id, &$sql) {
      echo "Testing server reporting...\n";
      $resp = SimpleHEAD::HEADrequest($url);
      $useslm = 0;
      $usesetag = 0;
      if (!isset($resp['lm']) || empty($resp['lm'])) {
        echo "The bloody server doesn't report Last-Modified info.\n";
        $useslm = false;
      } else {
        echo "Aw yeah, it supports Last-Modified.\n";
        if (!ServerTestPackage::LMReliabilityTest($url)) {
          echo "Last-Modified timestamps are unreliable!\n";
          $useslm = false;
        } else {
          echo "Last-Modified timestamps are reliable.\n";
          $useslm = true;
        }
      }
      if (!isset($resp['et']) || empty($resp['et'])) {
        echo "The bloody server doesn't report ETag info.\n";
        $usesetag = false;
      } else {
        echo "Aw yeah, it supports ETag.\n";
        $usesetag = true;
      }
      echo "Storing these details in the database...";
      if ($usesetag === true && $useslm === true) {
        // supports both etag and last-modified
        $sql->write('test', 'reports', $id, 'complete');
        $sql->write('test', 'etag', $id, $resp['et']);
        $sql->write('test', 'lastmodified', $id, date('Y-m-d H:i:s', strtotime($resp['lm'])));
      } elseif ($usesetag === true && $useslm === false) {
        // supports only etag (I don't expect this to happen)
        $sql->write('test', 'reports', $id, 'etagonly');
        $sql->write('test', 'etag', $id, $resp['et']);
      } elseif ($usesetag === false && $useslm === true) {
        // supports only last-modified
        $sql->write('test', 'reports', $id, 'lmonly');
        $sql->write('test', 'lastmodified', $id, date('Y-m-d H:i:s', strtotime($resp['lm'])));
      } elseif ($usesetag === false && $useslm === false) {
        // supports neither etag nor last-modified (bad server!)
        $sql->write('test', 'reports', $id, 'none');
      } else {
        // ambiguous test results
        die("Ambiguous test results: useslm = $useslm, usesetag = $usesetag.\n");
      }
      echo "Done.\n";
    }
    
    // a test to see if Last-Modified timestamps change between requests
    function LMReliabilityTest($url) {
      echo "Testing reliability of Last-Modified timestamp...\n";
    
      $max = 3; // number of consecutive queries to perform
      $unreliable = false;
      
      for ($i = 1; $i <= $max; $i++) {
        $resp = SimpleHEAD::HEADrequest($url);
        echo "ping $i: $resp[lm]\t\t";
        $ping[$i] = strtotime($resp['lm']);
        if ($i == 1) {
          echo "starting value\n";
        } else {
          echo "change from last: ";
          if ($ping[$i]-$ping[$i-1] != 0) {
            $unreliable = true;
            echo Duration::toString($ping[$i]-$ping[$i-1])."\n";
          } else {
            echo "0 seconds\n";
          }
        }
        // wait 5 seconds and check again. if Last-Modified changes, it is unreliable
        if ($i < $max) sleep(3);
      }
      if ($unreliable) {
        return false;
      }
      return true;
    }
  }
?>