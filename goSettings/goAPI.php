<?php
 /**
 * @file 		goAPI.php
 * @brief 		API for Settings
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jericho James Milo  <james@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
*/

	include_once ("../MySQLiDB.php");
    include_once ("../goDBasterisk.php");
    include_once ("../goDBgoautodial.php");
    include_once ("../goDBkamailio.php");
    include_once ("../goFunctions.php");

	$astDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);
	$goDB = new MySQLiDB($VARDBgo_server, $VARDBgo_user, $VARDBgo_pass, $VARDBgo_database);
	$kamDB = new MySQLiDB($VARDBgokam_server, $VARDBgokam_user, $VARDBgokam_pass, $VARDBgokam_database);
    
    ####### Variables #########
    
    if (isset($_GET["goAction"])) {
            $goAction = $_GET["goAction"];
    } elseif (isset($_POST["goAction"])) {
            $goAction = $_POST["goAction"];
    }
    
    if (isset($_GET["goUser"])) {
            $goUser = $_GET["goUser"];
    } elseif (isset($_POST["goUser"])) {
            $goUser = $_POST["goUser"];
    }
    
    if (isset($_GET["goPass"])) {
            $goPass = $_GET["goPass"];
    } elseif (isset($_POST["goPass"])) {
            $goPass = $_POST["goPass"];
    }
    
    if (isset($_GET["goURL"])) {
            $goURL = $_GET["goURL"];
    } elseif (isset($_POST["goURL"])) {
            $goURL = $_POST["goURL"];
    }
    
    $goCharset = "UTF-8";
    $goVersion = "1.0";
    
    #### check credentials ####
	$pass_hash = '';
	$cwd = $_SERVER['DOCUMENT_ROOT'];
	$bcrypt = 0;

	$user = preg_replace("/\'|\"|\\\\|;| /", "", $goUser);
	$pass = preg_replace("/\'|\"|\\\\|;| /", "", $goPass);
	
    //$query_settings = "SELECT pass_hash_enabled FROM system_settings";
    $rslt = $astDB->getOne('system_settings', 'pass_hash_enabled');
	$pass_hash_enabled = $rslt['pass_hash_enabled'];

	//$passSQL = "pass='$pass'";
	if ($pass_hash_enabled > 0) {
		if ($bcrypt < 1) {
			$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
			$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
		} else {$pass_hash = $pass;}
		//$passSQL = "pass_hash='$pass_hash'";
		$astDB->where('pass_hash', $pass_hash);
	} else {
		$astDB->where('pass', $pass);
	}
	
    //$query_user = "SELECT user,pass FROM vicidial_users WHERE user='$goUser' AND $passSQL";
	$astDB->where('user', $goUser);
    $rslt = $astDB->getOne('vicidial_users', 'user,pass');
    $check_result = $astDB->getRowCount();
    
    if ($check_result > 0) {
       
        if (file_exists($goAction . ".php" )) {
            include $goAction . ".php";
           #$apiresults = array( "result" => "success", "message" => "Command Not Found" );
        } else {
    		$apiresults = array( "result" => "error", "message" => "Command Not Found" );
        }
    
    } else {
        
        $apiresults = array( "result" => "error", "message" => "Invalid Username/Password" );
        
    }
    
    
    $userresponsetype = $_REQUEST["responsetype"];
    
    if (( $userresponsetype != $responsetype && ( $userresponsetype != "xml" && $userresponsetype != "json" ) )) {
    	$userresponsetype = "xml";
    }
    
    #### API OUTPUT ####
    ob_start();
    
    if (count( $apiresults )) {
    	if ($userresponsetype == "json") {
    		$apiresults = json_encode( $apiresults );
    		echo $apiresults;
    		exit();
    	} else {
    		if ($userresponsetype == "xml") {
    			echo "<?xml version=\"1.0\" encoding=\"" . $goCharset . "\"?>\n<goautodialapi version=\"" . $goVersion . ( "\">\n<action>" . $action . "</action>\n" );
    			apiXMLOutput( $apiresults );
    			echo "</goautodialapi>";
    		} else {
    			if ($responsetype) {
    				exit( "result=error;message=This API function can only return XML response format;" );
    			}
    
    			foreach ($apiresults as $k => $v) {
    				echo "" . $k . "=" . $v . ";";
    			}
    		}
    	}
    }
    
    $apioutput = ob_get_contents();
    ob_end_clean();
    echo $apioutput;
?>
