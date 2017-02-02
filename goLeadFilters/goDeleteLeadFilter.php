<?php
    #######################################################
    #### Name: goDeleteLeadFilter.php	               ####
    #### Description: API to delete specific Lead Filter  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
        $lead_filter_id = mysqli_real_escape_string($link, $_REQUEST['lead_filter_id']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check lead filter ID if its null or empty
	if($lead_filter_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Lead Filter ID."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


   		$query = "SELECT lead_filter_id FROM vicidial_lead_filters $ul where lead_filter_id='$lead_filter_id';";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataLeadFilterID = mysqli_real_escape_string($link, $fresults['lead_filter_id']);
			}

			if(!$dataLeadFilterID == null) {
				$deleteQuery = "DELETE FROM vicidial_lead_filters WHERE lead_filter_id='$dataLeadFilterID';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted FIlter Lead ID $dataLeadFilterID','DELETE FROM vicidial_lead_filters WHERE lead_filter_id=$dataLeadFilterID;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead Filter ID: $dataLeadFilterID", $log_group, $deleteQuery);


				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Lead Filter doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Lead Filter doesn't exist.");
		}
	}//end
?>
