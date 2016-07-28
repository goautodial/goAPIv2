<?php
    #######################################################
    #### Name: getAllPauseCodes.php 	               ####
    #### Description: API to get all Pause Code in a specific campaign      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";

		$camp = $_REQUEST['pauseCampID'];
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

                $query = "SELECT campaign_id, pause_code,pause_code_name,billable FROM vicidial_pause_codes $ul WHERE campaign_id ='$camp' ORDER BY pause_code;";
   		$rsltv = mysqli_query($link,$query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataCampID[] = $fresults['campaign_id'];
       			$dataPC[] = $fresults['pause_code'];
       			$dataPCN[] = $fresults['pause_code_name'];
       			$dataBill[] = $fresults['billable'];
 	  		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "pause_code" => $dataPC, "pause_code_name" => $dataPCN, "billable" => $dataBill);
		}
	}

?>
