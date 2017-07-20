<?php
    ////////////////////////////////////#
    //# Name: goDeleteUser.php  	               //#
    //# Description: API to delete specific User      //#
    //# Version: 0.9                                  //#
    //# Copyright: GOAutoDial Ltd. (c) 2011-2015      //#
    //# Written by: Jeremiah Sebastian V. Samatra     //#
    //# License: AGPLv2                               //#
    ////////////////////////////////////#
    include_once ("../goFunctions.php");
    
    // POST or GET Variables
    $user_id = mysqli_real_escape_string($link, $_REQUEST['user_id']);
	$user = mysqli_real_escape_string($link, $_REQUEST['user']);
	$action = mysqli_real_escape_string($link, $_REQUEST['action']);
    $ip_address = $_REQUEST['hostname'];
    $goUser = $_REQUEST['goUser'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

	if(empty($user_id) && empty($user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	} elseif(empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	}else {
		
		$groupId = go_get_groupid($session_user);
		if (!checkIfTenant($groupId)) {
			$ul = "AND user_id='$user_id'";
		} else { 
			$ul = "AND user_id='$user_id' AND user_group='$groupId'";  
		}
		if ($groupId != 'ADMIN') {
			$notAdminSQL = "AND user_group != 'ADMIN'";
		}
		
		if($action == "delete_selected"){
			if(!empty($user)){
				$exploded = explode(",",$user);
			}else{
				$exploded = explode(",",$user_id);
			}
			
			$error_count = 0;
			$string_return = "";
			$test = array();
			for($i=0;$i < count($exploded);$i++){
				if(!empty($user))
				$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' AND user = '".$exploded[$i]."';";
				else
				$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' AND user_id = '".$exploded[$i]."';";
				
				$selectResult = mysqli_query($link, $selectQuery) or die(mysqli_error($link));
				$numResult = mysqli_num_rows($selectResult);
				array_push($test, $selectQuery);
				if($numResult > 0){
					while($fresults = mysqli_fetch_array($selectResult)){
						$dataUserID = $fresults["user"];
						$dataPhoneLogin = $fresults["phone_login"];
						
						$deleteQuery = "DELETE FROM vicidial_users WHERE user='$dataUserID' $notAdminSQL"; 
						$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
						
						$deleteQueryGo = "DELETE FROM users WHERE name='$dataUserID'"; 
						$deleteResultGo = mysqli_query($linkgo, $deleteQueryGo) or die(mysqli_error($linkgo));
						
						$deleteQueryA = "DELETE FROM phones WHERE extension='$dataPhoneLogin';";
						$deleteResultA = mysqli_query($link, $deleteQueryA) or die(mysqli_error($link));
						
						$deleteQueryCB = "DELETE FROM subscriber WHERE username='$dataPhoneLogin';";
						$deleteResultCB = mysqli_query($linkgokam, $deleteQueryCB) or die(mysqli_error($linkgokam));
						
					}
				}else{
					$error_count = $error_count + 1;
				}
				
				
				$querydel = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' AND user='$dataUserID' $notAdminSQL ORDER BY user ASC LIMIT 1;";
				$rsltvdel = mysqli_query($link, $querydel) or die(mysqli_error($db));
				$countResult = mysqli_num_rows($rsltvdel);
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
				
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User: $dataUserID", $log_group, $deleteQuery);
				
			}
			
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Delete Failed");
			} else {
				$apiresults = array("result" => "success", "query" => $test); 
			}
			
		}else{
			if(!empty($user)){
				$ul = "AND user = '$user'";
			}else{
				$ul = "AND user_id = '$user_id'";
			}
			
			$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' $ul;";
			$selectResult = mysqli_query($link, $selectQuery) or die(mysqli_error($link));
			$numResult = mysqli_num_rows($selectResult);
			
			if($numResult > 0){
				
				while($fresults = mysqli_fetch_array($selectResult)){
					$dataUserID = $fresults["user"];
					$dataPhoneLogin = $fresults["phone_login"];
				}
				
				$deleteQuery = "DELETE FROM vicidial_users WHERE user='$dataUserID' AND user != 'ADMIN'"; 
				$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
				
				$deleteQueryGo = "DELETE FROM users WHERE name='$dataUserID'"; 
				$deleteResultGo = mysqli_query($linkgo, $deleteQueryGo) or die(mysqli_error($linkgo));
				
				$deleteQueryA = "DELETE FROM phones WHERE extension='$dataPhoneLogin';";
				$deleteResultA = mysqli_query($link, $deleteQueryA) or die(mysqli_error($link));
				
				$deleteQueryCB = "DELETE FROM subscriber WHERE username='$dataPhoneLogin';";
				$deleteResultCB = mysqli_query($linkgokam, $deleteQueryCB) or die(mysqli_error($linkgokam));
				
				$querydel = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
				$rsltvdel = mysqli_query($link, $querydel);
				$countResult = mysqli_num_rows($rsltvdel);
				
				if($countResult > 0) {
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Delete Failed");
				} else {
					$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User: $dataUserID", $log_group, $deleteQuery);
					
					$apiresults = array("result" => "success"); 
				}
			}else{
				$err_msg = error_handle("41004");
				$apiresults = array("code" => "41004", "result" => $err_msg);
			}
		}
	}
?>
