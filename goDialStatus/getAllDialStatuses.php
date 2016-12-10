<?php
    #######################################################
    #### Name: getAllCountryCodes.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $campaign_id = $_REQUEST['campaign_id'];
	$get_hotkeys_only = $_REQUEST['hotkeys_only'];
	
	if (isset($get_hotkeys_only) && $get_hotkeys_only > 0) {
		$where_human_answered = "WHERE human_answered='Y'";
		$and_human_answered = "AND human_answered='Y'";
	}
    
    $query = "SELECT status,status_name
            FROM vicidial_statuses $where_human_answered
            UNION SELECT status,status_name
                FROM vicidial_campaign_statuses
            WHERE campaign_id='$campaign_id' $and_human_answered ORDER BY status";
   	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
   		$apiresults = array(
                        "result" => "success",
                        "status" => $dataStatus,
                        "status_name" => $dataStatusName,
						"test" => $query
                    );
	}
?>