<?php
    #######################################################
    #### Name: getAllDialStatuses.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### Modified by: Chris Lomuntad                   ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
	
	$hotkeys_only = mysqli_escape_string($link, $_REQUEST['hotkeys_only']);
	$campaign_id = mysqli_escape_string($link, $_REQUEST['campaign_id']);
	
	$human_answered = '';
	if ($hotkeys_only === "1") {
		$human_answered = "WHERE human_answered='Y'";
	}
	
    $query = "SELECT status,status_name
				FROM vicidial_statuses
				$human_answered
				ORDER BY status";
   	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
	}
	
	if (strlen($human_answered) > 0 && strlen($campaign_id) > 0) {
		$query = "SELECT status,status_name
					FROM vicidial_campaign_statuses
					$human_answered
					AND campaign_id='$campaign_id'
					ORDER BY status";
		$rsltv = mysqli_query($link, $query);
		
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataStatus[] = $fresults['status'];
			$dataStatusName[] = $fresults['status_name'];
		}
	}
	
	$apiresults = array(
		"result" => "success",
		"status" => $dataStatus,
		"status_name" => $dataStatusName,
		"test" => $query
	);
?>