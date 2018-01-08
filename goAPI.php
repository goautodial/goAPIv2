<?php
    /***************************************************
    #### Name: goAPI.php                            ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ***************************************************/
   
    include_once ("MySQLiDB.php"); 
    include_once ("goDBasterisk.php");
    include_once ("goDBgoautodial.php");
    include_once ("goDBkamailio.php");
    include_once ("goFunctions.php");

    ### Check if DB variables are not set ###
	$VARDB_server   = (!isset($VARDB_server)) ? "localhost" : $VARDB_server;
	$VARDB_user     = (!isset($VARDB_user)) ? "justgocloud" : $VARDB_user;
	$VARDB_pass     = (!isset($VARDB_pass)) ? "justgocloud1234" : $VARDB_pass;
	$VARDB_database = (!isset($VARDB_database)) ? "asterisk" : $VARDB_database;

	$VARDBgo_server   = (!isset($VARDBgo_server)) ? "localhost" : $VARDBgo_server;
	$VARDBgo_user     = (!isset($VARDBgo_user)) ? "goautodialu" : $VARDBgo_user;
	$VARDBgo_pass     = (!isset($VARDBgo_pass)) ? "pancit8888" : $VARDBgo_pass;
	$VARDBgo_database = (!isset($VARDBgo_database)) ? "goautodial" : $VARDBgo_database;

	$VARDBgokam_server   = (!isset($VARDBgokam_server)) ? "localhost" : $VARDBgokam_server;
	$VARDBgokam_user     = (!isset($VARDBgokam_user)) ? "kamailio" : $VARDBgokam_user;
	$VARDBgokam_pass     = (!isset($VARDBgokam_pass)) ? "kamailiorw" : $VARDBgokam_pass;
	$VARDBgokam_database = (!isset($VARDBgokam_database)) ? "kamailio" : $VARDBgokam_database;
     ### End of DB variables ###

    
    /* Variables */
    
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
    
    $default_users = array('VDAD','goAPI','goautodial','VDCL');

    $goCharset = "UTF-8";
    $goVersion = "1.0";
    
    $astDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);
    $goDB = new MySQLiDB($VARDBgo_server, $VARDBgo_user, $VARDBgo_pass, $VARDBgo_database);
    $kamDB = new MySQLiDB($VARDBgokam_server, $VARDBgokam_user, $VARDBgokam_pass, $VARDBgokam_database);

    /* check credentials */
	$pass_hash = '';
	$cwd = $_SERVER['DOCUMENT_ROOT'];
	$bcrypt = 0;

	$user = preg_replace("/\'|\"|\\\\|;| /", "", $goUser);
	$pass = preg_replace("/\'|\"|\\\\|;| /", "", $goPass);
	
    //$query_settings = "SELECT pass_hash_enabled FROM system_settings";
    $pass_hash_enabled = $astDB->getValue("system_settings", "pass_hash_enabled", NULL);

	$passSQL = "pass='$pass'";
	if ($pass_hash_enabled > 0) {
		if ($bcrypt < 1) {
			$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
			$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
		} else {$pass_hash = $pass;}
		$passSQL = "pass_hash='$pass_hash'";
	}
	
    //$query_user = "SELECT user,pass FROM vicidial_users WHERE user='$goUser' AND $passSQL";
    //$rslt=mysqli_query($link, $query_user);
    $astDB->where("user", $goUser);
    if($pass_hash_enabled > 0 )
    	$astDB->where("pass_hash", $pass_hash);
    else
	$astDB->where("pass", $pass);
    $check_result = $astDB->getValue("vicidial_users", "count(*)", NULL);
    
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
    
    
    $userresponsetype = $_REQUEST["responsetype"];
    
    if (( $userresponsetype != $responsetype && ( $userresponsetype != "xml" && $userresponsetype != "json" ) )) {
    	$userresponsetype = "xml";
    }
    
    /* API OUTPUT */
    ob_start();
    
    if (count( $apiresults )) {
    	if ($userresponsetype == "json") {
    		$apiresults = json_encode( $apiresults );
    		echo $apiresults;
    		exit();
    	} else {
    		if ($userresponsetype == "xml") {
    			echo '<?xml version="1.0" encoding="' . $goCharset . '"?>\n<goautodialapi version="'.$goVersion.'">(\n<action>"'. $action .' "</action>\n" )';
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

