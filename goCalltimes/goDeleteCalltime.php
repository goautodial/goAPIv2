<?php
    #######################################################
    #### Name: goDeletCalltime.php	               ####
    #### Description: API to delete specific Calltime  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
        //$calltime = mysql_real_escape_string($_REQUEST['call_time_id']);
        //$call_time_id = mysqli_real_escape_string($_REQUEST['call_time_id']);
        $call_time_id = $_REQUEST['call_time_id'];
        $call_time_id = mysqli_real_escape_string($link, $call_time_id);
	$ip_address = mysqli_real_escape_string($_REQUEST['log_ip']);
	$log_user = mysqli_real_escape_string($_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
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

				
   		$queryOne = "SELECT call_time_id FROM vicidial_call_times $ul where call_time_id='".$call_time_id."';";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_call_times WHERE call_time_id = '$call_time_id';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Calltime ID $call_time_id','DELETE FROM vicidial_call_times WHERE call_time_id=$call_time_id;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Calltime ID $call_time_id", $log_group, $deleteQuery);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Calltime doesn't exist.");
		}
	}//end
?>
