<?php
  require_once('libs/PEAR/HTTP/Request.php');        // passes proper HTTP requests

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