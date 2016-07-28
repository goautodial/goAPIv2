<?php
    #######################################################
    #### Name: getStateCallTimesInfo.php 	               ####
    #### Description: API to get specific STate Call Times       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    $state_call_time_id = $_REQUEST["state_call_time_id"]; 

        if($state_call_time_id == null) {
                $apiresults = array("result" => "Error: Set a value for State Call Time ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT state_call_time_id, state_call_time_state, state_call_time_name, sct_default_start, sct_default_stop, user_group from vicidial_state_call_times WHERE state_call_time_id='".mysqli_real_escape_string($state_call_time_id)."' $ul $addedSQL ORDER BY state_call_time_id LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataStateID[] = $fresults['state_call_time_id'];
                        $dataStateState[] = $fresults['state_call_time_state'];
                        $dataStateName[] = $fresults['state_call_time_name'];
                        $dataDefStart[] = $fresults['sct_default_start'];
                        $dataDefStop[] = $fresults['sct_default_stop'];
                        $dataUserGroup[] = $fresults['user_group'];
                        $apiresults = array("result" => "success", "state_call_time_id" => $dataStateID, "state_call_time_state" => $dataStateState, "state_call_time_name" => $dataStateName, "sct_default_start" => $dataDefStart, "sct_default_stop" => $dataDefStop, "user_group" => $dataUserGroup);
                }
	        } else {

                $apiresults = array("result" => "Error: Lead Filter does not exist.");

        	}
        	}
?>
