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
    
	$hotkeys_only = $astDB->escape($_REQUEST['hotkeys_only']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	
	$selectable = '';
	if ($hotkeys_only === "1") {
		$selectable = "WHERE selectable='Y'";
	}
	
	if (strlen($selectable) > 0 && strlen($campaign_id) > 0) {
		$query = "SELECT status,status_name
					FROM vicidial_campaign_statuses
					$selectable
					AND campaign_id='$campaign_id'
					ORDER BY status";
		$rsltv = $astDB->rawQuery($query);
		
		foreach ($rsltv as $fresults){
			$dataStatus[] = $fresults['status'];
			$dataStatusName[] = $fresults['status_name'];
		}
	}
	
    $query = "SELECT status,status_name
				FROM vicidial_statuses
				$selectable
				ORDER BY status";
   	$rsltv = $astDB->rawQuery($query);
    
    foreach ($rsltv as $fresults){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
	}
	
	$apiresults = array(
		"result" => "success",
		"status" => $dataStatus,
		"status_name" => $dataStatusName,
		"test" => $query
	);
?>