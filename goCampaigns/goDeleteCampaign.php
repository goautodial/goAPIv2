<?php
    #######################################################
    #### Name: goDeleteCampaign.php	               ####
    #### Description: API to delete specific campaign  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    ### POST or GET Variables
    $campaign_id = $_REQUEST['campaign_id'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
		$log_user = $_REQUEST['log_user'];
		$log_group = $_REQUEST['log_group'];
    
    ### Check campaign_id if its null or empty
	if($campaign_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE campaign_id='$campaign_id'";
    		} else { 
			$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT campaign_id,campaign_name FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataCampID = $fresults['campaign_id'];
			}

			if(!$dataCampID == null) {
				$deleteQuery = "DELETE FROM vicidial_campaigns WHERE campaign_id='$dataCampID';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Campaign $dataCampID','DELETE FROM vicidial_campaigns WHERE campaign_id=$dataCampID;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $dataCampID", $log_group, $deleteQuery);


				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Campaign doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Campaign doesn't exist.");
		}
	}//end
?>
