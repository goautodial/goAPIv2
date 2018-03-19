<?php
    #######################################################
    #### Name: getAllCountryCodes.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    
    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    
    //$query = "SELECT status,status_name
    //        FROM vicidial_campaign_statuses 
    //        WHERE campaign_id='$campaign_id'
    //        ORDER BY status";
	$astDB->where('campaign_id', $campaign_id);
	$astDB->orderBy('status', 'desc');
   	$rsltv = $astDB->get('vicidial_campaign_statuses', null, 'status,status_name');
    
    foreach ($rsltv as $fresults){
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