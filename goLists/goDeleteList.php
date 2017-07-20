<?php
    //////////////////////////////////////////////////////
    /// Name: goDeleteList.php 		///
    /// Description: API to delete specific List 		///
    /// Version: 0.9 		///
    /// Copyright: GOAutoDial Inc. (c) 2011-2014 		///
    /// Written by: Jeremiah Sebastian Samatra 		///
    /// License: AGPLv2 		///
    /////////////////////////////////////////////////////
    include_once ("../goFunctions.php");
    
    // POST or GET Variables
    $list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
	$action = strtolower(mysqli_real_escape_string($link, $_REQUEST['action']));
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);
		
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
	if($list_id == null) {
		$err_msg = error_handle("10107");
		$apiresults = array("code" => "10107", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for List ID."); 
	} elseif(empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}else{
		if($action == strtolower("delete_selected")){
			$exploded = explode(",",$list_id);
			$error_count = 0;
			for($i=0;$i < count($exploded);$i++){
				$query = "SELECT list_id,list_name FROM vicidial_lists WHERE list_id='".$exploded[$i]."' order by list_id LIMIT 1";
				$rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
				$countResult = mysqli_num_rows($rsltv);
				
				if($countResult > 0) {
					while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
						$dataListID = $fresults['list_id'];
					}
					if($dataListID != null) {
						$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';"; 
						$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
						$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';"; 
						$deleteResultLeads = mysqli_query($link, $deleteQueryLeads) or die(mysqli_error($link));
						$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;"; 
						$deleteResultStmt = mysqli_query($link, $deleteQueryStmt) or die(mysqli_error($link));
						//echo $deleteQuery.$deleteQueryLeads.$deleteQueryStmt;
						
					// Admin Logs
						$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted List ID: $dataListID", $log_group, $deleteQuery);
						
						$apiresults = array("result" => "success");
					} else {
						$error_count = $error_count + 1;
					}
		
				} else {
					$error_count = $error_count + 1;
				}
			}
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "( $error_count ) Errors Found: Delete Failed");
			} else {
				$apiresults = array("result" => "success"); 
			}
		}else{
			$groupId = go_get_groupid($session_user);
			if (!checkIfTenant($groupId)) {
				$ul = "WHERE list_id='$list_id'";
			} else { 
				$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";  
			}
			
			$query = "SELECT list_id,list_name FROM vicidial_lists $ul order by list_id LIMIT 1";
			$rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
			$countResult = mysqli_num_rows($rsltv);
			
			if($countResult > 0) {
				while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataListID = $fresults['list_id'];
				}
				if($dataListID != null) {
					$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';"; 
					$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
					$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';"; 
					$deleteResultLeads = mysqli_query($link, $deleteQueryLeads) or die(mysqli_error($link));
					$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;"; 
					$deleteResultStmt = mysqli_query($link, $deleteQueryStmt) or die(mysqli_error($link));
					//echo $deleteQuery.$deleteQueryLeads.$deleteQueryStmt;
					
				// Admin Logs
					$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted List ID: $dataListID", $log_group, $deleteQuery);
					
					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: List doesn't exist.");
				}
	
			} else {
				$apiresults = array("result" => "Error: List doesn't exist.");
			}
		}
	}//end
?>
