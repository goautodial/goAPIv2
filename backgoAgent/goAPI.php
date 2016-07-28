<?php
####################################################
#### Name: goAPI.php                            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################
//ini_set('display_errors', 'on');
//error_reporting(E_ALL);

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

if (isset($_GET['goCampaign'])) { $campaign = $astDB->escape($_GET['goCampaign']); }
    else if (isset($_POST['goCampaign'])) { $campaign = $astDB->escape($_POST['goCampaign']); }

if (isset($_GET['goPhone'])) { $phone_login = $_GET['goPhone']; }
    else if (isset($_POST['goPhone'])) { $phone_login = $_POST['goPhone']; }

if (isset($_GET['goPhonePass'])) { $phone_pass = $_GET['goPhonePass']; }
    else if (isset($_POST['goPhonePass'])) { $phone_pass = $_POST['goPhonePass']; }

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
        $query = $astDB->rawQuery("SELECT vdc_agent_api_access FROM vicidial_users WHERE user='$goUser' AND pass='$goPass'");
        if (!$query) {
            $APIResult = array( "result" => "error", "message" => "Invalid Username/Password" );
        } else {
            $allowedAPIAccess = $query[0]['vdc_agent_api_access'];
            if ($allowedAPIAccess) {
                if (!preg_match("/goGetAllowedCampaigns|goLogoutUser/", $goAction) && (!isset($campaign) || $campaign == '')) {
                    $APIResult = array( "result" => "error", "message" => "Please select a campaign" );
                } else {
                    include("{$goAction}.php");
                }
            } else {
                $APIResult = array( "result" => "error", "message" => "User '$goUser' is NOT allowed to access GOagent API" );
            }
        }
    } else {
        $APIResult = array( "result" => "error", "message" => "Command NOT Found" );
    }
} else {
    $APIResult = array( "result" => "error", "message" => "goAction should NOT be empty" );
}

$userResponseType = $_REQUEST["responsetype"];

if (!isset($userResponseType) || strlen($userResponseType) < 1) {
    $userResponseType = "xml";
}

### API OUTPUT ###
ob_start();

if (count($APIResult)) {
    if ($userResponseType == "json") {
        $APIResult = json_encode( $APIResult );
        echo $APIResult;
        exit();
    } else if ($userResponseType == "xml") {
        if (isset($goAction) && $goAction != "") {
            $xml_data->addChild("action", htmlspecialchars("$goAction"));
        }
        array_to_xml( $APIResult, $xml_data);
        echo $xml_data->asXML();
    } else {
        exit( "result=error;message=This API function only accepts XML or JSON value on responsetype;" );
        //echo implode_recur(';', $APIResult);
    }
}

$APIOutput = ob_get_contents();
ob_end_clean();

parse_xml($APIOutput);
?>