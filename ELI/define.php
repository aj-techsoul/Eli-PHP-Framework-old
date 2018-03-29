<?php

/**
 *  ELI FRAMEWORK
 *  FRAMEWORK CREATED BY AJAY KUMAR (TECHSOUL.IN) // EAGLE LITE - ELI FRAMEWORK
 *  +91 9862542983
 *  techsoul4@gmail.com
 */

// DATABASE CONFIG
//------------------------------------
//------------------------------------

$GLOBALS['config']['dbhost'] = "localhost"; // FOR SQLITE, Please mention SQLlite File Path
$GLOBALS['config']['dbuser'] = "root";
$GLOBALS['config']['dbpass'] = "";
$GLOBALS['config']['dbname'] = "eli";


//------------------------------------
//   SITE DETAILS
//------------------------------------
$GLOBALS['impMail'] = "techsoul4@gmail.com";
$GLOBALS['site']['name'] = "ELI";
$GLOBALS['site']['author'] = "AJ";
//------------------------------------
//------------------------------------



// IMPORTANT TO SET
$websitepath = "eagle-lte-fw/";


// DEFINE
//------------------------------------
//------------------------------------
define('WWW','www/');
define("ELI","ELI/");
define("CLASS_DIR","ELI/classes/");
define("PROCESS","ELI/process/");
define('_BASEURL_',"http://".$_SERVER["SERVER_NAME"]."/".$websitepath);
define('_BASEPATH_',"http://".$_SERVER["SERVER_NAME"]."/".$websitepath.WWW);
define('PAGE',@$_GET['url']);
?>