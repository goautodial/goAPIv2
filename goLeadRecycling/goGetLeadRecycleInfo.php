<?php
    ###########################################################
    #### Name: getPauseCodeInfo.php 	                   ####
    #### Description: API to get specific Lead Recycle     ####
    #### Version: 0.9                                      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016          ####
    #### Written by: Warren Ipac Briones                   ####
    #### License: AGPLv2                                   ####
    ###########################################################
    include "goFunctions.php";
        $camp = $_REQUEST['leadRecCampID'];
        $status = $_REQUEST['status'];

        if($camp == null || $status == null) {
                $apiresults = array("result" => "Error: Set a value for campaign ID and status.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT status, attempt_delay, attempt_maximum, active, campaign_id FROM vicidial_lead_recycle WHERE campaign_id='$camp' AND status='$status' $ul $addedSQL ORDER BY status LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataCampID[] = $fresults['campaign_id'];
       			$dataStatus[] = $fresults['status'];
       			$dataAttemptDelay[] = $fresults['attempt_delay'];
       			$dataAttemptMax[] = $fresults['attempt_maximum'];
                        $dataActive[] = $fresults['active'];
 	  		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status" => $dataStatus, "attempt_delay" => $dataAttemptDelay, "attempt_maximum" => $dataAttemptMax, "active" => $dataActive);

                }
	        } else {

                $apiresults = array("result" => "Error: Lead Filter does not exist.");

        	}
        	}
?>
