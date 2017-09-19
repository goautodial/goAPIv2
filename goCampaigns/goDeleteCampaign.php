<?php
    ////////////////////////////////////#
    //# Name: goDeleteCampaign.php	               //#
    //# Description: API to delete specific campaign  //#
    //# Version: 0.9                                  //#
    //# Copyright: GOAutoDial Inc. (c) 2011-2014      //#
    //# Written by: Jerico James Milo                 //#
    //# License: AGPLv2                               //#
    ////////////////////////////////////#
    include_once("../goFunctions.php");
    
    // POST or GET Variables
	$campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
	$action = strtolower(mysqli_real_escape_string($link, $_REQUEST['action']));
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
	$log_user = $_REQUEST['log_user'];
	$log_group = $_REQUEST['log_group'];
	
    // Check campaign_id if its null or empty
	if(empty($campaign_id) || empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg); 
		//$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
		
		$groupId = go_get_groupid($session_user);
		if (!checkIfTenant($groupId)) {
			$ul = "WHERE campaign_id='$campaign_id'";
		} else { 
			$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";  
		}
		
		if(!empty($action) && $action == strtolower("delete_selected")){
			$exploded = explode(",",$campaign_id);
			$error_count = 0;
			for($i=0;$i < count($exploded);$i++){
				
				$deleteQuery = "DELETE FROM vicidial_campaigns WHERE campaign_id='".$exploded[$i]."';"; 
				$deleteResult = mysqli_query($link, $deleteQuery);
				
				$deleteDispo = "DELETE FROM vicidial_campaigns_statuses WHERE campaign_id='".$exploded[$i]."';"; 
				$deleteResult2 = mysqli_query($link, $deleteDispo);
				
				$deleteRecycle = "DELETE FROM vicidial_lead_recycle WHERE campaign_id = '".$exploded[$i]."';";
				$deleteResult3 = mysqli_query($link, $deleteRecycle);

				$querydel = "SELECT campaign_id FROM vicidial_campaigns $ul;";
				$rsltvdel = mysqli_query($link, $querydel);
				$countResult = mysqli_num_rows($rsltvdel);
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
					
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $deleteQuery);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Dispositions in Campaign ID: $campaign_id", $log_group, $deleteDispo);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead Recycles in Campaign ID: $campaign_id", $log_group, $deleteRecycle);
			}
				
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg); 
				//$apiresults = array("result" => "Error: Delete Failed");
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
				
				$deleteDispo = "DELETE FROM vicidial_campaigns_statuses WHERE campaign_id='".$campaign_id."';"; 
				$deleteResult2 = mysqli_query($link, $deleteDispo);
				
				$deleteRecycle = "DELETE FROM vicidial_lead_recycle WHERE campaign_id = '".$campaign_id."';";
				$deleteResult3 = mysqli_query($link, $deleteRecycle);

				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $deleteQuery);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Dispositions in Campaign ID: $campaign_id", $log_group, $deleteDispo);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead Recycles in Campaign ID: $campaign_id", $log_group, $deleteRecycle);
				
				$apiresults = array("result" => "success");
			} else {
				$err_msg = error_handle("41004", "campaign. Doesn't exist");
				$apiresults = array("code" => "41004", "result" => $err_msg); 
				//$apiresults = array("result" => "Error: Campaign doesn't exist.");
			}
		}
		
	}//end
?>
