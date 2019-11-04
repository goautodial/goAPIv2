<?php
/**
 * @file    	goAPI.php
 * @brief     	API to handle every API
 * @copyright   Copyright (C) 2019 GOautodial Inc.
 * @author      Jerico James Flores Milo  <jericojames@goautodial.com>
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
    include_once ("../goDBasterisk.php");
    include_once ("../goDBgoautodial.php");
    include_once ("../goDBkamailio.php");
    include_once ("../goFunctions.php");
    
    /* Check if DB variables are not set */
	$VARDB_server   = (!isset($VARDB_server)) ? "localhost" : $VARDB_server;
	$VARDB_user     = (!isset($VARDB_user)) ? "asterisku" : $VARDB_user;
	$VARDB_pass     = (!isset($VARDB_pass)) ? "asterisku1234" : $VARDB_pass;
	$VARDB_database = (!isset($VARDB_database)) ? "asterisk" : $VARDB_database;

	$VARDBgo_server   = (!isset($VARDBgo_server)) ? "localhost" : $VARDBgo_server;
	$VARDBgo_user     = (!isset($VARDBgo_user)) ? "goautodialu" : $VARDBgo_user;
	$VARDBgo_pass     = (!isset($VARDBgo_pass)) ? "goautodialu1234" : $VARDBgo_pass;
	$VARDBgo_database = (!isset($VARDBgo_database)) ? "goautodial" : $VARDBgo_database;

	$VARDBgokam_server   = (!isset($VARDBgokam_server)) ? "localhost" : $VARDBgokam_server;
	$VARDBgokam_user     = (!isset($VARDBgokam_user)) ? "kamailiou" : $VARDBgokam_user;
	$VARDBgokam_pass     = (!isset($VARDBgokam_pass)) ? "kamailiou1234" : $VARDBgokam_pass;
	$VARDBgokam_database = (!isset($VARDBgokam_database)) ? "kamailio" : $VARDBgokam_database;
    /* End of DB variables */
    
    /* Variables */    
    if (isset($_GET["goAction"])) { $goAction = $astDB->escape($_GET["goAction"]); } 
		elseif (isset($_POST["goAction"])) { $goAction = $astDB->escape($_POST["goAction"]); }
    
    if (isset($_GET["goUser"])) { $goUser = $astDB->escape($_GET["goUser"]); } 
		elseif (isset($_POST["goUser"])) { $goUser = $astDB->escape($_POST["goUser"]); }
    
    if (isset($_GET["goPass"])) { $goPass = $astDB->escape($_GET["goPass"]); } 
		elseif (isset($_POST["goPass"])) { $goPass = $astDB->escape($_POST["goPass"]); }
    
    if (isset($_GET["goURL"])) { $goURL = $astDB->escape($_GET["goURL"]); }
		else if (isset($_POST["goURL"])) { $goURL = $astDB->escape($_POST["goURL"]); }
    
	if (isset($_GET['responsetype'])) { $userResponseType = $astDB->escape($_GET['responsetype']); }
		else if (isset($_POST['responsetype'])) { $userResponseType = $astDB->escape($_POST['responsetype']); }    
		
	/* Standard goAPI variables */
    $log_user     = $session_user;
    $log_group    = go_get_groupid($session_user, $astDB);     
    $log_ip       = $astDB->escape($_REQUEST['log_ip']);
    $goUser       = $astDB->escape($_REQUEST['goUser']);
    $goPass       = (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']));		
		
    define('DEFAULT_USERS', array('VDAD','VDCL', 'goAPI'));

    $goCharset = "UTF-8";
    $goVersion = "4.0";
    
	##### getting timezone ######
    $goDB->where('setting', 'timezone');
    $rslt = $goDB->getOne('settings', 'value');
    $tz = $rslt['value'];
	if (!empty($tz)) {
        ini_set('date.timezone', $tz);
        date_default_timezone_set($tz);
	}
    
    /* check credentials */
	$pass_hash = '';
	$cwd = $_SERVER['DOCUMENT_ROOT'];
	$bcrypt = 0;

	$user = preg_replace("/\'|\"|\\\\|;| /", "", $goUser);
	$pass = preg_replace("/\'|\"|\\\\|;| /", "", $goPass);
	
    //$query_settings = "SELECT pass_hash_enabled FROM system_settings";
    $system_settings = $astDB->getOne("system_settings", "pass_hash_enabled,pass_cost,pass_key");

	$passSQL = "pass='$pass'";
	if ($system_settings['pass_hash_enabled'] > 0) {
		if ($bcrypt < 1) {
			$pass_hash = encrypt_passwd($pass, $system_settings['pass_cost'], $system_settings['pass_key']);
		} else {$pass_hash = $pass;}
		$passSQL = "pass_hash='$pass_hash'";
	}
	
    //$query_user = "SELECT user,pass FROM vicidial_users WHERE user='$goUser' AND $passSQL";
    //$rslt=mysqli_query($link, $query_user);
    $astDB->where("user", $goUser);
    if($system_settings['pass_hash_enabled'] > 0 )
    	$astDB->where("pass_hash", $pass_hash);
    else
	   $astDB->where("pass", $pass);
    $astDB->getOne("vicidial_users", "count(*) as sum");
    $check_result = $astDB->count;
	
    if ($check_result > 0) {       
        if (file_exists($goAction . ".php" )) {
            include $goAction . ".php";
            //$apiresults = array( "result" => "success", "message" => "Command Not Found" );
        } else {
    		$apiresults = array( "result" => "error", "message" => "Command Not Found" );
        }    
    } else {        
        $apiresults = array( "result" => "error", "message" => "Invalid Username/Password" );        
    }
    
	if (!isset($userResponseType) || strlen($userResponseType) < 1) {
		$userResponseType = "xml";
	}
    
    /* API OUTPUT */
    ob_start();
    
	if (count($apiresults)) {
		if ($userResponseType == "json") {
			$apiresults = json_encode( $apiresults );
			echo $apiresults;
			exit();
		} else if ($userResponseType == "xml") {
			echo '<?xml version="1.0" encoding="' . $goCharset . '"?>\n<goautodialapi version="'.$goVersion.'">(\n<action>"'. $goAction .' "</action>\n" )';
                apiXMLOutput( $apiresults );
                echo "</goautodialapi>";
		} else {
			exit( "result=error;message=This API function only accepts XML or JSON value on responsetype;" );
			//echo implode_recur(';', $apiresults);
		}
	}

    $apioutput = ob_get_contents();
    ob_end_clean();
    echo $apioutput;
?>

