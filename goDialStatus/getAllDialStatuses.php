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
    
    $query = "SELECT status,status_name
            FROM vicidial_statuses
            UNION SELECT status,status_name
                FROM vicidial_campaign_statuses
            where campaign_id='$campaign_id' ORDER BY status";
   	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
   		$apiresults = array(
                        "result" => "success",
                        "status" => $dataStatus,
                        "status_name" => $dataStatusName,
                    );
	}
?>