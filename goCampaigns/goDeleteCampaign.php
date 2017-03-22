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
	$campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
	$action = strtolower(mysqli_real_escape_string($link, $_REQUEST['action']));
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
		
		if($action == strtolower("delete_selected")){
			$exploded = explode(",",$campaign_id);
			$error_count = 0;
			for($i=0;$i < count($exploded);$i++){
				
				$deleteQuery = "DELETE FROM vicidial_campaigns WHERE campaign_id='".$exploded[$i]."';"; 
				$deleteResult = mysqli_query($link, $deleteQuery);
				
				$querydel = "SELECT campaign_id FROM vicidial_campaigns $ul;";
				$rsltvdel = mysqli_query($link, $querydel);
				$countResult = mysqli_num_rows($rsltvdel);
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
					
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $deleteQuery);
				
			}
				
			if($error_count > 0) {
				$apiresults = array("result" => "Error: Delete Failed");
			} else {
				$apiresults = array("result" => "success"); 
			}
		}else{
			$querycheck = "SELECT campaign_id FROM vicidial_campaigns $ul;";
			$first_check_query = mysqli_query($link, $querycheck);
			$first_check = mysqli_num_rows($first_check_query);
			
			if($first_check > 0) {
				$deleteQuery = "DELETE FROM vicidial_campaigns WHERE campaign_id='$campaign_id';"; 
				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;
				
		### Admin logs
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $deleteQuery);
				
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Campaign doesn't exist.");
			}
		}
		
	}//end
?>
