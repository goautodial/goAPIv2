<?php
    #######################################################
    #### Name: goDeleteStateCallTime.php	               ####
    #### Description: API to delete specific State Call Time ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
        $state_call_time_id = mysqli_real_escape_string($_REQUEST['state_call_time_id']);
        $ip_address = mysqli_real_escape_string($_REQUEST['hostname']);
    
    ### Check Voicemail ID if its null or empty
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

				
   		$queryOne = "SELECT state_call_time_id FROM vicidial_state_call_times $ul where state_call_time_id='".mysqli_escape_string($state_call_time_id)."';";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_state_call_times WHERE state_call_time_id= '$state_call_time_id';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;
/*
DELETE FROM vicidial_state_call_times WHERE state_call_time_id
*/

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted State Call Time ID $state_call_time_id','DELETE FROM vicidial_state_call_times WHERE state_call_time_id=$state_call_time_id;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: State Call Menu doesn't exist.");
		}
	}//end
?>
