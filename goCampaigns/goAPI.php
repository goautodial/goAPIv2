<?php
####################################################
#### Name: goAPI.php                            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################
// ini_set('display_errors', 'on');
// error_reporting(E_ALL);

$webRoot = $_SERVER['DOCUMENT_ROOT'];
$version = file_get_contents("{$webRoot}/version.txt");
$goCharset = "UTF-8";
$goVersion = "1.0";

include_once('./includes/MySQLiDB.php');
@include_once('../goDBasterisk.php');
@include_once('../goDBgoautodial.php');
@include_once('../goFunctions.php');
include_once('./includes/XMLParser.php');

$astDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);
$goDB = new MySQLiDB($VARDBgo_server, $VARDBgo_user, $VARDBgo_pass, $VARDBgo_database);

### Variables ###
if (isset($_GET['goAction'])) { $goAction = $_GET['goAction']; }
    else if (isset($_POST['goAction'])) { $goAction = $_POST['goAction']; }

if (isset($_GET['goUser'])) { $goUser = $astDB->escape($_GET['goUser']); }
    else if (isset($_POST['goUser'])) { $goUser = $astDB->escape($_POST['goUser']); }

if (isset($_GET['goPass'])) { $goPass = $astDB->escape($_GET['goPass']); }
    else if (isset($_POST['goPass'])) { $goPass = $astDB->escape($_POST['goPass']); }

if (isset($_GET['goURL'])) { $goPass = $astDB->escape($_GET['goURL']); }
    else if (isset($_POST['goURL'])) { $goPass = $astDB->escape($_POST['goURL']); }

$US='_';
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$loginDATE = date("Ymd");

$SIPserver = 'kamailio'; // Put 'asterisk' if not using 'kamailio'.
### End Variables ###

### Check Credentials ###
$path = getcwd();
$files = scandir($path);
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $fileName = str_replace('.php', '', $file);
        if (!preg_match('/^(index|goAPI|includes)$/', $fileName)) {
            $fileList[] = $fileName;
        }
    }
}
$actions = implode('|', $fileList);
if (isset($goAction) && $goAction != "") {
    if (preg_match("/$actions/", $goAction)) {
        $query = $astDB->rawQuery("SELECT user,pass FROM vicidial_users WHERE user='$goUser' AND pass='$goPass'");
        if (!$query) {
            $apiresults = array( "result" => "error", "message" => "Invalid Username/Password" );
        } else {
            include("{$goAction}.php");
        }
    } else {
        $apiresults = array( "result" => "error", "message" => "Command NOT Found" );
    }
} else {
    $apiresults = array( "result" => "error", "message" => "goAction should NOT be empty" );
}

$userResponseType = $_REQUEST["responsetype"];

if (!isset($userResponseType) || strlen($userResponseType) < 1) {
    $userResponseType = "xml";
}

### API OUTPUT ###
ob_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');

if (count($apiresults)) {
    if ($userResponseType == "json") {
        $apiresults = json_encode( $apiresults );
        echo $apiresults;
        exit();
    } else if ($userResponseType == "xml") {
        if (isset($goAction) && $goAction != "") {
            $xml_data->addChild("action", htmlspecialchars("$goAction"));
        }
        array_to_xml( $apiresults, $xml_data);
        echo $xml_data->asXML();
    } else {
        exit( "result=error;message=This API function only accepts XML or JSON value on responsetype;" );
        //echo implode_recur(';', $apiresults);
    }
}

$APIOutput = ob_get_contents();
ob_end_clean();

echo $APIOutput;
?>