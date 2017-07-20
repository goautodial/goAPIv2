<?php
    ////////////////////////////////////////////////////////////
    /// Name: goDeleteUserGroup.php 		///
    /// Description: API to delete specific User Group 		///
    /// Version: 0.9 		///
    /// Copyright: GOAutoDial Inc. (c) 2011-2014 		///
    /// Written by: Jeremiah Sebastian V. Samatra 		///
    /// License: AGPLv2 		///
    ////////////////////////////////////////////////////////////
    include_once ("../goFunctions.php");
    
    // POST or GET Variables
    $user_group = $_REQUEST['user_group'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    
    
	if($user_group == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User Group."); 
	} else {
		$groupId = go_get_groupid($goUser);
		if (!checkIfTenant($groupId)) {
				$ul = "WHERE user_group='$user_group'";
		} else { 
			$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";  
		}
		
		$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
		$countResult = mysqli_num_rows($rsltv);
		
		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataUserGroup = $fresults['user_group'];
			}
			
			if(!$dataUserGroup == null) {
				$deleteQuery = "DELETE FROM vicidial_user_groups WHERE user_group='$dataUserGroup' AND user_group != 'ADMIN';"; 
				$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
				$deleteQueryA = "DELETE FROM user_access_group WHERE user_group='$dataUserGroup' AND user_group != 'ADMIN';";
				$deleteResultA = mysqli_query($linkgo, $deleteQueryA) or die(mysqli_error($link));
				
			} else {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "Error: User Group doesn't exist.");
			}
			
		} else {
			$err_msg = error_handle("41004", "user_group. Does not exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: User Group doesn't exist.");
		}
		
		$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
		$countResult = mysqli_num_rows($rsltv);
	
		if($countResult > 0) {
			$err_msg = error_handle("41004", "user_group");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		}else{
			$apiresults = array("result" => "success");
			$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $deleteQuery);
		}
	}//end
?>
