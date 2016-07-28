<?php
    #######################################################
    #### Name: getCalltimesInfo.php 	               ####
    #### Description: API to get specific Calltime     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";
    $call_time_id = $_REQUEST["call_time_id"]; 

        if($call_time_id == null) {
                $apiresults = array("result" => "Error: Set a value for Calltime ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT call_time_id,call_time_name,ct_default_start,ct_default_stop,user_group FROM vicidial_call_times WHERE call_time_id='$call_time_id' $ul $addedSQL ORDER BY call_time_id LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                        $dataCalltimeID[] = $fresults['call_time_id'];
                        $dataCalltimeName[] = $fresults['call_time_name'];
                        $dataCtDefStart[] = $fresults['ct_default_start'];
                        $dataCtDefStop[] = $fresults['ct_default_stop'];
                        $dataUserGroup[] = $fresults['user_group'];
                        $apiresults = array("result" => "success", "call_time_id" => $dataCalltimeID, "call_time_name" => $dataCalltimeName, "ct_default_start" => $dataCtDefStart, "ct_default_stop" => $dataCtDefStop, "user_group" => $dataUserGroup);
                }
	        } else {

                $apiresults = array("result" => "Error: Calltime does not exist.");

        	}
        	}
?>
