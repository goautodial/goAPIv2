<?php
    #######################################################
    #### Name: getAllCalltimes.php 	               ####
    #### Description: API to get all calltimes         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT call_time_id,call_time_name,ct_default_start,ct_default_stop,user_group FROM vicidial_call_times $ul $addedSQL ORDER BY call_time_id;";
   		$rsltv = mysqli_query($link,$query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataCalltimeID[] = $fresults['call_time_id'];
       			$dataCalltimeName[] = $fresults['call_time_name'];
       			$dataCtDefStart[] = $fresults['ct_default_start'];
       			$dataCtDefStop[] = $fresults['ct_default_stop'];
       			$dataUserGroup[] = $fresults['user_group'];
 	  		$apiresults = array("result" => "success", "call_time_id" => $dataCalltimeID, "call_time_name" => $dataCalltimeName, "ct_default_start" => $dataCtDefStart, "ct_default_stop" => $dataCtDefStop, "user_group" => $dataUserGroup);
		}

?>
