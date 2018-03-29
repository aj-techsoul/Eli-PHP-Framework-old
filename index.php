<?php
/**
 *  ELI FRAMEWORK
 *  FRAMEWORK CREATED BY AJAY KUMAR (TECHSOUL.IN) // EAGLE LITE - ELI FRAMEWORK
 *  +91 9862542983
 *  techsoul4@gmail.com
 */


// DISPLAY ERROR CODE

$display_error = true;
if($display_error)
{
ini_set('display_errors', '1');
error_reporting(E_ALL);
}
else
{
ini_set('display_errors', '0');
error_reporting(0);    
}

//////////////////////// HACK PROOF SCRIPT - AJ ///////////////////////
//ini_set("session.use_cookies", 0);
ini_set("session.use_only_cookies", 0);
ini_set("session.use_trans_sid", 1);
ini_set("session.cache_limiter", "nocache");
//session_cache_expire(10080);
ini_set('session.cookie_httponly', 1);
ini_set('expose_php', 'off');

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);
// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);
session_start();
session_regenerate_id();
/////////////////////////////////////////////////
//header("Cache-Control", "no-store, no-cache, must-revalidate");
$headers = apache_request_headers();
$headers['Cookie'] = ""; //"PHPSESSID=SECURED-".md5("ajib".time());
///////////////////////////////////////////////////////////////////////
// DEFINE CORE ELI
include('ELI/functions.php');
include(PROCESS.'url.php');
/////////////////////////////////
?>
