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

	require_once('SimpleHEAD.php');               // passes simple HTTP HEAD requests
  require_once('Duration.php');    // writes human readable time durations
  
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
				$sql->write('feeds', array(
					'reports' => 'complete', 
					'etag' => $resp['et'], 
					'lastmodified' => date('Y-m-d H:i:s', strtotime($resp['lm']))
				), $id);
      } elseif ($usesetag === true && $useslm === false) {
        // supports only etag (I don't expect this to happen)
        $sql->write('feeds', array(
					'reports' => 'etagonly', 
					'etag' => $resp['et'], 
				), $id);
      } elseif ($usesetag === false && $useslm === true) {
        // supports only last-modified
        $sql->write('feeds', array(
					'reports' => 'lmonly', 
					'lastmodified' => date('Y-m-d H:i:s', strtotime($resp['lm']))
				), $id);
      } elseif ($usesetag === false && $useslm === false) {
        // supports neither etag nor last-modified (bad server!)
        $sql->write('feeds', array('reports' => 'none'), $id);
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