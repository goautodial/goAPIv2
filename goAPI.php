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
    require_once (__DIR__ . "/goDBasterisk.php");
    require_once (__DIR__ . "/goDBgoautodial.php");
    require_once (__DIR__ . "/goDBkamailio.php");
    require_once (__DIR__ . "/goFunctions.php");

    /** @var MySQLiDB $astDB */
    /** @var MySQLiDB $goDB */
    /** @var MySQLiDB $kamDB */

    /* Check if DB variables are not set */
	if (!isset($VARDB_server)) { $VARDB_server = "localhost"; }
	if (!isset($VARDB_user)) { $VARDB_user = "asterisku"; }
	if (!isset($VARDB_pass)) { $VARDB_pass = "asterisku1234"; }
	if (!isset($VARDB_database)) { $VARDB_database = "asterisk"; }

	if (!isset($VARDBgo_server)) { $VARDBgo_server = "localhost"; }
	if (!isset($VARDBgo_user)) { $VARDBgo_user = "goautodialu"; }
	if (!isset($VARDBgo_pass)) { $VARDBgo_pass = "goautodialu1234"; }
	if (!isset($VARDBgo_database)) { $VARDBgo_database = "goautodial"; }

	if (!isset($VARDBgokam_server)) { $VARDBgokam_server = "localhost"; }
	if (!isset($VARDBgokam_user)) { $VARDBgokam_user = "kamailiou"; }
	if (!isset($VARDBgokam_pass)) { $VARDBgokam_pass = "kamailiou1234"; }
	if (!isset($VARDBgokam_database)) { $VARDBgokam_database = "kamailio"; }
    /* End of DB variables */

    /* Variables */
    $goAction = '';
    $goUser = '';
    $goPass = '';
    $goURL = '';
    $userResponseType = '';

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

    $session_user = $session_user ?? '';
    $log_user     = $session_user;
    $log_group    = go_get_groupid($session_user, $astDB);
    $log_ip       = $astDB->escape($_REQUEST['log_ip'] ?? '');
    $goUser       = $astDB->escape(($_REQUEST['goUser'] ?? ''));
    $goPass       = (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass'] ?? ''));

    define('DEFAULT_USERS', ['VDAD','VDCL', 'goAPI']);

    $goCharset = "UTF-8";
    $goVersion = "4.0";

	##### getting timezone ######
    $goDB->where('setting', 'timezone');
    $rslt = $goDB->getOne('settings', 'value');
    $tz = is_array($rslt) ? ($rslt['value'] ?? '') : '';
	if (!empty($tz)) {
        ini_set('date.timezone', $tz);
        date_default_timezone_set($tz);
	}

    /* check credentials */
	$user = preg_replace("/\'|\"|\\\\|;| /", "", (string) $goUser);
	$pass = (string) $goPass;

    //$query_settings = "SELECT pass_hash_enabled FROM system_settings";
    $system_settings = $astDB->getOne("system_settings", "pass_hash_enabled,pass_cost,pass_key");
    $system_settings = is_array($system_settings) ? $system_settings : [];

    //$query_user = "SELECT user,pass FROM vicidial_users WHERE user='$goUser'";
    //$rslt=mysqli_query($link, $query_user);
    $astDB->where("user", $goUser);
    $rslt = $astDB->getOne("vicidial_users", "pass,pass_hash");
    $storedHash = $rslt['pass_hash'] ?? '';
    $storedPass = $rslt['pass'] ?? '';
    $check_result = 0;

    if (($system_settings['pass_hash_enabled'] ?? 0) > 0) {
        if (is_string($storedHash) && preg_match('/^\$2y\$/', $storedHash)) {
            $check_result = (password_verify($pass, $storedHash) || hash_equals($storedHash, $pass)) ? 1 : 0;
        }
    } else {
        $check_result = hash_equals((string) $storedPass, $pass) ? 1 : 0;
    }

    if ($check_result > 0) {
        //$includeAction = basename(realpath($goAction . ".php"));
        if (!str_contains((string) $goAction, '/') && file_exists($goAction . ".php")) {
            include $goAction . ".php";
            //$apiresults = array( "result" => "success", "message" => "Command Not Found" );
        } else {
    		$apiresults = [ "result" => "error", "message" => "Command Not Found" ];
        }
    } else {
        $apiresults = [ "result" => "error", "message" => "Invalid Username/Password" ];
    }

	if (!isset($userResponseType) || strlen($userResponseType) < 1) {
		$userResponseType = "xml";
	}

    /* API OUTPUT */
    ob_start();
    header("Access-Control-Allow-Origin: *");

	if (isset($apiresults) && (is_countable($apiresults) ? count($apiresults) : 0)) {
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
