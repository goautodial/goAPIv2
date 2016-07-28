<?php
    #######################################################
    #### Name: goDeleteScript.php	               ####
    #### Description: API to delete specific Script ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
        $script_id = mysqli_real_escape_string($link, $_REQUEST['script_id']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    
    ### Check Voicemail ID if its null or empty
	if($script_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Script ID."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                }

				
   		$queryOne = "SELECT script_id FROM vicidial_scripts where script_id='$script_id' $ul;";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_scripts WHERE script_id= '$script_id';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);

				$deleteQuery1 = "DELETE FROM go_scripts WHERE script_id= '$script_id';"; 
   				$deleteResult1 = mysqli_query($link, $deleteQuery1);

				$deleteQuery2 = "UPDATE FROM vicidial_campaigns SET campaign_script = '$campaign_script' WHERE campaign_script = '$script_id';"; 
   				$deleteResult2 = mysqli_query($link, $deleteQuery2);
				//echo $deleteQuery;
/*
DELETE FROM vicidial_state_call_times WHERE state_call_time_id
*/

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Script ID $script_id','DELETE FROM vicidial_scripts WHERE script_id=$state_call_time_id DELETE FROM go_scripts WHERE script_id=$script_id UPDATE vicidial_all_campaigns SET campaign_script = $campaign_script WHERE campaign_script=$script_id;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Script doesn't exist.");
		}
	}//end
?>
