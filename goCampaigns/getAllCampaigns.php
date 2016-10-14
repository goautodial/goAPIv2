<?php
    #######################################################
    #### Name: getAllCampaigns.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "WHERE user_group='$groupId'";  
	}

   	$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCampID[] = $fresults['campaign_id'];
       		$dataCampName[] = $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
		$dataDialMethod[] = $fresults['dial_method'];
		$dataActive[] = $fresults['active'];
   		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
	}
?>
