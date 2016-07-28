<?php
    ##############################################################################
    #### Name: getAllPauseCodes.php 	               			      ####
    #### Description: API to get all Lead Recycle in a specific campaign      ####
    #### Version: 0.9                               			      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016    			      ####
    #### Written by: Waren Ipac Briones 	      			      ####
    #### License: AGPLv2                               			      ####
    ##############################################################################
    include "goFunctions.php";

		$camp = $_REQUEST['leadRecCampID'];
                $groupId = go_get_groupid($goUser);


        if($camp == null) {
                $apiresults = array("result" => "Error: Set a value for Campaign ID.");
        } else {


                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT campaign_id,status,attempt_delay,attempt_maximum,active FROM vicidial_lead_recycle $ul WHERE campaign_id ='$camp' ORDER BY status;";
   		$rsltv = mysqli_query($link,$query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataCampID[] = $fresults['campaign_id'];
       			$dataStatus[] = $fresults['status'];
       			$dataAttemptDelay[] = $fresults['attempt_delay'];
       			$dataAttemptMax[] = $fresults['attempt_maximum'];
                        $dataActive[] = $fresults['active'];
 	  		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status" => $dataStatus, "attempt_delay" => $dataAttemptDelay, "attempt_maximum" => $dataAttemptMax,"active" => $dataActive);
		}
	}

?>
