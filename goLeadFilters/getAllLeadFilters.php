<?php
    #######################################################
    #### Name: getAllLeadFilters.php 	               ####
    #### Description: API to get all lead filter       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
     
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


   	$query = "SELECT lead_filter_id,lead_filter_name FROM vicidial_lead_filters $ul $addedSQL ORDER BY lead_filter_id;";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataLeadFilterID[] = $fresults['lead_filter_id'];
       		$dataLeadFilterName[] = $fresults['lead_filter_name'];
   		$apiresults = array("result" => "success", "lead_filter_id" => $dataLeadFilterID, "lead_filter_name" => $dataLeadFilterName);
	}

?>
