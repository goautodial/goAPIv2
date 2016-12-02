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

		$query = "SELECT * FROM vicidial_call_times WHERE call_time_id='$call_time_id' $ul $addedSQL ORDER BY call_time_id LIMIT 1;";
		$rsltv = mysqli_query($link,$query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
            $fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
			$apiresults = array_merge(array("result" => "success"), $fresults);
	    } else {
            $apiresults = array("result" => "Error: Calltime does not exist.");
        }
    }
?>
