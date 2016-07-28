<?php
    #######################################################
    #### Name: goGetAdminLogsList.php	               ####
    #### Description: API to get all admin logs        ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2016           ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    
    include_once ("goFunctions.php");
    /*
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "WHERE user_group='$groupId'";  
  	   }*/

   $query = "SELECT * FROM vicidial_admin_log $ul ORDER BY admin_log_id;";
   $rsltv = mysqli_query($link, $query);
   //$array = array();
   while($fresults = mysqli_fetch_array($rsltv, MYSQL_ASSOC)){
		$dataID[] = $fresults['admin_log_id'];	 
		$dataUser[] = $fresults['user'];
		$dataIP[] = $fresults['ip_address'];
		$dataDate[] = $fresults['event_date'];
		$dataSection[] = $fresults['event_section'];
		//$array[] = $fresults['event_type'];
		//$array[] = $fresults['record_id'];
		//$array[] = $fresults['event_code'];
		//$array[] = $fresults['event_sql'];
		//$array[] = $fresults['event_notes'];
		//$array[] = $fresults['user_group'];
		
		$apiresults = array("result" => "success", "admin_log_id" => $dataID, "user" => $dataUser, "ip_address" => $dataIP, "event_date" => $dataDate);
		//$apiresults = array_merge( array( "result" => "success" ), $array );
	}
?>
