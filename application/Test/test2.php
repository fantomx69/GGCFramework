<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo 'test2.php';

//abstract class a {
//    static $sa = '1';
//    
//    abstract static function y(); //errore
//}
//
//class b extends a {
//    static function y();
//}
//
//$b = new b();
//
//echo $b::$a; //errore.

//-------------------------------------------------

//$groupName[] = 'aaa->bbb->ccc';
//$groupName[] = 'ddd->eee->fff';
//$groupName[] = 'aaa->mmm';
//$groupName[] = 'bbb->hhh->zzz';
//$groupName[] = 'aaa->xxx';
//
//foreach ($groupName as $value) {
//    $aryKeys = explode('->', $value);
//
//    $ref = &$aryACL;
//
//    foreach ($aryKeys as $value) {
//        $ref = &$ref[$value];
//    }
//
//}    
//
//$ref['uno'] = 1;
//$ref['due'] = 2;
//
//var_dump($aryACL);
//print_r($aryACL);

//----------------------------------------------------------------

//function getPageURL() {
//        $pageURL = 'http';
//        
//        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {
//            $pageURL .= "s";
//        }
//        
//        $pageURL .= "://";
//        
//        if ($_SERVER["SERVER_PORT"] != "80") {
//            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
//        } else {
//            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
//        }
//        
//        return $pageURL;
//}
//include 'GGC_URL.php';
//
//$oURL = new GGC_URL(getPageURL(),
//        array(
//        GGC_URL::OPTION_STRICT           => true,
//        GGC_URL::OPTION_USE_BRACKETS     => true,
//        GGC_URL::OPTION_ENCODE_KEYS      => true,
//        GGC_URL::OPTION_SEPARATOR_INPUT  => '&',
//        GGC_URL::OPTION_SEPARATOR_OUTPUT => '&',
//        GGC_URL::OPTION_REQUEST_URL_AS_DEFAULT => true
//        ));
//
////echo 'Canonical URL : ' . $oURL->getCanonical() . '<br/>';
////echo 'Fragment URL : ' . $oURL->getFragment() . '<br/>';
////echo 'Normalized URL : ' . $oURL->getNormalizedURL() . '<br/>';
////echo 'Path URL : ' . $oURL->getPath() . '<br/>';
//echo 'Query : ' . $oURL->getQuery() . '<br/>';
//echo 'Path : ' . $oURL->getPath() . '<br/>';
//echo 'Canonical : ' . $oURL->getCanonical()->getQuery() . '<br/>';
//echo 'URL : ' . $oURL->getURL() . '<br/>';
//
//print_r($oURL->getQueryVariables());


//-------------------------------------------------------------------

//class uri {
//  
//  var $uri_string;
//  var $segments = array();
//  var $uri_protocol = 'auto';
//  
//  function fetch_uri_string() {
//   if (strtoupper($this->uri_protocol) == 'AUTO') {
//    if (is_array($_GET) AND count($_GET) == 1) {
//     $this->uri_string = key($_GET);
//     return;
//    }
//
//   $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
//    if ($path != '' AND $path != '/' AND $path != '/'.$_SERVER['PHP_SELF']) {
//     $this->uri_string = $path;
//     return;
//    }
//
//   $path = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
//    if ($path != '' AND $path != '/') {
//     $this->uri_string = $path;
//     return;
//    }
//
//   $path = (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO');
//    if ($path != '' AND $path != '/' AND $path != '/'.$_SERVER['PHP_SELF']) {
//     $this->uri_string = $path;
//     return;
//    }
//   } else {
//    $uri = strtoupper($this->uri_protocol);
//    if ($uri == 'REQUEST_URI') {
//     $this->uri_string = $this->parse_request_uri();
//     return;
//    }
//
//   $this->uri_string = (isset($_SERVER[$uri])) ? $_SERVER[$uri] : @getenv($uri);
//    if ($this->uri_string == '/') {
//     $this->uri_string = '';
//    }
//   }
//  }
//  
//  function parse_request_uri() {
//   if (!isset($_SERVER['REQUEST_URI']) OR $_SERVER['REQUEST_URI'] == '') {
//    return '';
//   }
//   $request_uri = preg_replace("|/(.*)|", "\\1", str_replace("\\", "/", $_SERVER['REQUEST_URI']));
//   if ($request_uri == '' OR $request_uri == $_SERVER['PHP_SELF']) {
//    return '';
//   }
//   $fc_path = $_SERVER['PATH_INFO'];
//   if (strpos($request_uri, '?') != FALSE) {
//    $fc_path .= '?';
//   }
//   $parsed_uri = explode('/', $request_uri);
//   $i = 0;
//   foreach(explode('/', $fc_path) as $segment) {
//    if (isset($parsed_uri[$i]) AND $segment == $parsed_uri[$i]) {
//     $i++;
//    }
//   }
//   $parsed_uri = implode('/', array_slice($parsed_uri, $i));
//   if ($parsed_uri != '') {
//    $parsed_uri = '/'.$parsed_uri;
//   }
//   return $parsed_uri;
//  }
//  function explode_segments() {
//   foreach(explode('/', preg_replace('|/*(.+?)/*$|', '\\1', $this->uri_string)) as $val) {
//    // Filter segments for security
//    //$val = trim($this->_filter_uri($val));
//
//   if ($val != '')
//     $this->segments[] = $val;
//   }
//      
//      
//      
//  }
//  function segment($n, $no_result = FALSE) {
//   return ( ! isset($this->segments[$n])) ? $no_result : $this->segments[$n];
//  }
// }
// 
// $uri = new uri;
//
//$uri->fetch_uri_string();
// echo $uri->uri_string;
// echo '<br/><br/>';
//
//$uri->explode_segments();
// print_r($uri->segments);
// echo '<br><br>';
//
//echo $uri->segment(0);
// echo '<br><br>';
// 
//echo print_r(parse_url($_SERVER['PATHINFO_FILENAME']));
// echo '<br><br>';
 

//-----------------------------------------------------------------------------
//function parse_utf8_url($url) 
// { 
//     static $keys = array('scheme'=>0,'user'=>0,'pass'=>0,'host'=>0,'port'=>0,'path'=>0,'query'=>0,'fragment'=>0); 
//     if (is_string($url) && preg_match( 
//             '~^((?P<scheme>[^:/?#]+):(//))?((\\3|//)?(?:(?P<user>[^:]+):(?P<pass>[^@]+)@)?(?P<host>[^/?:#]*))(:(?P<port>\\d+))?' . 
//             '(?P<path>[^?#]*)(\\?(?P<query>[^#]*))?(#(?P<fragment>.*))?~u', $url, $matches)) 
//     { 
//         foreach ($matches as $key => $value) 
//             if (!isset($keys[$key]) || empty($value)) 
//                 unset($matches[$key]); 
//         return $matches; 
//     } 
//     return false; 
// }
//
////$url = parse_url($_SERVER['REQUEST_URI']);
//$url = parse_utf8_url($_SERVER['REQUEST_URI']);
//echo 'ciao ' . '<br/>';
//print_r($url);

//----------------------------------------------------------------------------
//$stringa = 'Ciao sono una stringa';
//
//$l = strlen($stringa);
//for($i=0; $i<$l; $i++) {
//    echo $stringa[$i];
//}
 
//----------------------------------------------------------------------------
include 'GGC_URI.php';
//include 'GGC_String.php';

function curPageURL() {
 $pageURL = 'http';
// if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 
 
 return $pageURL;
}

$oURI = new GGC_URI(curPageURL());

echo 'Page URL : ' . curPageURL() . '<br/><br/>';
echo 'URI : ' . $oURI->getURI() . '<br/>';
echo 'Scheme : ' . $oURI->getScheme() . '<br/>';
echo 'UserName : ' . $oURI->getUserName() . '<br/>';
echo 'Password : ' . $oURI->getPassword() . '<br/>';
echo 'HostName : ' . $oURI->getHostName() . '<br/>';
echo 'Port : ' . $oURI->getPort() . '<br/>';
echo 'Path : ' . $oURI->getPath() . '<br/>';
echo 'Query : ' . $oURI->getQuery() . '<br/>';
echo 'QueryVariables : '  . '<br/>';
print_r($oURI->getQueryVariables());
echo '<br/>';
echo 'Fragment : ' . $oURI->getFragment() . '<br/>';
echo 'Full Script Name : ' . __FILE__ . '<br/>';
echo 'Short Script Name : ' . basename(__FILE__) . '<br/>';
echo 'prova : ' . 'aaa/bbb/nome.php/' . '<br/>';
echo 'prova : ' . basename('aaa/bbb/nome.php/') . '<br/>';
echo 'prova : ' . GGC_String::right('aaa/bbb/nome.php/', 5);


?>