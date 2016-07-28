<?php
    #######################################################
    #### Name: getLeadFilterInfo.php 	               ####
    #### Description: API to get specific lead filter       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    $lead_filter_id = $_REQUEST["lead_filter_id"]; 

        if($lead_filter_id == null) {
                $apiresults = array("result" => "Error: Set a value for Lead Filter ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


   	$query = "SELECT lead_filter_id,lead_filter_name FROM vicidial_lead_filters where lead_filter_id='$lead_filter_id' $ul $addedSQL ORDER BY lead_filter_id LIMIT 1;";
   	$rsltv = mysqli_query($link, $query);
	$exist = mysqli_num_rows($rsltv);
	if($exist >= 1){
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataLeadFilterID[] = $fresults['lead_filter_id'];
       		$dataLeadFilterName[] = $fresults['lead_filter_name'];
   		$apiresults = array("result" => "success", "lead_filter_id" => $dataLeadFilterID, "lead_filter_name" => $dataLeadFilterName);
	}
	} else {

                $apiresults = array("result" => "Error: Lead Filter does not exist.");

	}
	}
?>
