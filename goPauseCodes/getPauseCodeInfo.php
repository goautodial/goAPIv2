<?php
    #######################################################
    #### Name: getPauseCodeInfo.php 	               ####
    #### Description: API to get specific Pause Code       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";
        $camp = $_REQUEST['pauseCampID'];
        $pause_code = $_REQUEST['pause_code'];

        if($camp == null || $pause_code == null) {
                $apiresults = array("result" => "Error: Set a value for campaign ID and pause code.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT pause_code, pause_code_name, billable, campaign_id FROM vicidial_pause_codes WHERE campaign_id='$camp' AND pause_code='$pause_code' $ul $addedSQL ORDER BY pause_code LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataCampID[] = $fresults['campaign_id'];
       			$dataPC[] = $fresults['pause_code'];
       			$dataPCN[] = $fresults['pause_code_name'];
       			$dataBill[] = $fresults['billable'];
 	  		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "pause_code" => $dataPC, "pause_code_name" => $dataPCN, "billable" => $dataBill);

                }
	        } else {

                $apiresults = array("result" => "Error: Lead Filter does not exist.");

        	}
        	}
?>
