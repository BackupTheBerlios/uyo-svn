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

  require_once('HTTPRequest.php');        // passes proper HTTP requests
  require_once("$GLOBALS[PROJECT_BASEDIR]/libs/PEAR.php");

  // all HEAD requests performed are of the same simple variant, so I made a class to avoid code duplication
  class SimpleHEAD {
    function SimpleHEAD() {
    }

    // sends a HTTP HEAD request for more information about the feed
    function HEADrequest($url) {
      $req =& new HTTP_Request($url);
      $req->setMethod(HTTP_REQUEST_METHOD_HEAD);
      if(!PEAR::isError($msg = $req->sendRequest())) {
        $lm = $req->getResponseHeader('last-modified');
        $et = $req->getResponseHeader('etag');
      } else {
        die("Error sending HTTP HEAD request! Error returned: ".$msg->getMessage()."\n");
      }
      return array('lm' => $lm, 'et' => $et);
    }
  }
?>