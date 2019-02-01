<?php
    ####################################################
    #### Name: goAPI.php                            ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once ("../goDBasterisk.php");
    include_once ("../goDBgoautodial.php");
    include_once ("../goFunctions.php");
    
    $version = file_get_contents('../version.txt');
    
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
    $query_user = "SELECT user,pass FROM vicidial_users WHERE user='$goUser' AND pass='$goPass'";
    $rslt=mysqli_query($link, $query_user);
    $check_result = mysqli_num_rows($rslt);
    
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
