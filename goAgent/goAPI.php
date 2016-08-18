<?php
####################################################
#### Name: goAPI.php                            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################
ini_set('display_errors', 'on');
error_reporting(E_ALL);

$webRoot = $_SERVER['DOCUMENT_ROOT'];
$version = file_get_contents("{$webRoot}/version.txt");
$goCharset = "UTF-8";
$goVersion = "1.0";

include_once('./includes/MySQLiDB.php');
@include_once('../goDBasterisk.php');
@include_once('../goDBgoautodial.php');
@include_once('../goFunctions.php');
include_once('./includes/XMLParser.php');

### Check if DB variables are not set ###
$VARDB_server   = (!isset($VARDB_server)) ? "162.254.144.92" : $VARDB_server;
$VARDB_user     = (!isset($VARDB_user)) ? "justgocloud" : $VARDB_user;
$VARDB_pass     = (!isset($VARDB_pass)) ? "justgocloud1234" : $VARDB_pass;
$VARDB_database = (!isset($VARDB_database)) ? "asterisk" : $VARDB_database;

$VARDBgo_server   = (!isset($VARDBgo_server)) ? "162.254.144.92" : $VARDBgo_server;
$VARDBgo_user     = (!isset($VARDBgo_user)) ? "goautodialu" : $VARDBgo_user;
$VARDBgo_pass     = (!isset($VARDBgo_pass)) ? "pancit8888" : $VARDBgo_pass;
$VARDBgo_database = (!isset($VARDBgo_database)) ? "goautodial" : $VARDBgo_database;
### End of DB variables ###

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

if (isset($_GET['goPhone'])) { $phone_login = $astDB->escape($_GET['goPhone']); }
    else if (isset($_POST['goPhone'])) { $phone_login = $astDB->escape($_POST['goPhone']); }

if (isset($_GET['goPhonePass'])) { $phone_pass = $astDB->escape($_GET['goPhonePass']); }
    else if (isset($_POST['goPhonePass'])) { $phone_pass = $astDB->escape($_POST['goPhonePass']); }

if (isset($_GET['goSIPServer'])) { $SIPserver = $astDB->escape($_GET['goSIPServer']); }
    else if (isset($_POST['goSIPServer'])) { $SIPserver = $astDB->escape($_POST['goSIPServer']); }

if (isset($_GET['bcrypt'])) { $bcrypt = $astDB->escape($_GET['bcrypt']); }
    else if (isset($_POST['bcrypt'])) { $bcrypt = $astDB->escape($_POST['bcrypt']); }

if (isset($_GET['responsetype'])) { $userResponseType = $astDB->escape($_GET['responsetype']); }
    else if (isset($_POST['responsetype'])) { $userResponseType = $astDB->escape($_POST['responsetype']); }

$auth = 0;
$US = '_';
$CL = ':';
$AT = '@';
$DS = '-';
$StarTtimE = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$loginDATE = date("Ymd");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");

while (strlen($CIDdate) > 9) {$CIDdate = substr("$CIDdate", 1);}
$check_time = ($StarTtimE - 86400);

$secX = date("U");
$hour = date("H");
$min = date("i");
$sec = date("s");
$mon = date("m");
$mday = date("d");
$year = date("Y");
$isdst = date("I");
$Shour = date("H");
$Smin = date("i");
$Ssec = date("s");
$Smon = date("m");
$Smday = date("d");
$Syear = date("Y");

$SIPserver = (!isset($SIPserver)) ? 'kamailio' : $SIPserver; // Put 'asterisk' if not using 'kamailio'.
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
        $system = get_settings('system', $astDB);
        $bcrypt = (isset($bcrypt)) ? $bcrypt : 1;
        $err_message = "Login incorrect, please try again";
        $auth_message = user_authorization($astDB, $goUser, $goPass, '', 1, $bcrypt, 0);
        if ($auth_message == 'GOOD')
            {$auth = 1;}
        if ($auth_message == 'LOCK')
            {$err_message = "Too many login attempts, try again in 15 minutes";}
        if ($auth_message == 'ERRNETWORK')
            {$err_message = "Too many network errors, please contact your administrator";}
        if ($auth_message == 'ERRSERVERS')
            {$err_message = "No available servers, please contact your administrator";}
        if ($auth_message == 'ERRPHONES')
            {$err_message = "No available phones, please contact your administrator";}
        if ($auth_message == 'ERRDUPLICATE')
            {$err_message = "You are already logged in, please log out of your other session first";}
        if ($auth_message == 'ERRAGENTS')
            {$err_message = "Too many agents logged in, please contact your administrator";}


        if ($auth < 1) {
            $APIResult = array( "result" => "error", "message" => $err_message );
        } else {
            $astDB->where('user', $goUser);
            $rslt = $astDB->getOne('vicidial_users', 'vdc_agent_api_access');
            $allowedAPIAccess = $rslt['vdc_agent_api_access'];
            if ($allowedAPIAccess) {
                if (!preg_match("/goGetScriptContents|goCheckConference|goGetLoginInfo|goGetAllowedCampaigns|goLogoutUser|goManualDialLookCall|goClearAPIField|goGetLabels|goXFERSendRedirect/", $goAction) && (!isset($campaign) || $campaign == '')) {
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

if (!isset($userResponseType) || strlen($userResponseType) < 1) {
    $userResponseType = "xml";
}

### API OUTPUT ###
ob_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
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
