<?php
    #######################################################
    #### Name: goDeletePhone.php	               ####
    #### Description: API to delete specific Phone     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $extension = $_REQUEST['extension'];
	$action = mysqli_real_escape_string($link, $_REQUEST['action']);
	$ip_address = $_REQUEST['hostname'];
	
	$groupId = go_get_groupid($session_user);
	$log_user = $session_user;
	$log_group = $groupId;
    
    ### Check campaign_id if its null or empty
	if($extension == null) { 
		$apiresults = array("result" => "Error: Set a value for Extension."); 
	} else {
		if (!checkIfTenant($groupId)) {
			$ul = "WHERE extension='$extension'";
		} else {
			$ul = "WHERE extension='$extension' AND user_group='$groupId'";
		}
		
		if($action == "delete_selected"){
			$exploded = explode(",",$extension);
			$error_count = 0;
			$string_return = "";
			for($i=0;$i < count($exploded);$i++){
				$query = "SELECT extension  FROM phones WHERE extension='".$exploded[$i]."';";
				$rsltv = mysqli_query($link,$query);
				
				while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataExtension = $fresults['extension'];
					$deleteQuery = "DELETE FROM phones WHERE extension = '$dataExtension'"; 
					$deleteResult = mysqli_query($link,$deleteQuery);
					//echo $deleteQuery;
					$deleteQueryB = "DELETE FROM subscriber where username= '$dataExtension'"; 
					$deleteResultB = mysqli_query($linkgokam,$deleteQueryB);
				}
				
				$query = "SELECT extension  FROM phones  WHERE extension='".$dataExtension."';";
				$rslta = mysqli_query($link,$query);
				$countResult = mysqli_num_rows($rslta);
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
				
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Phone $extension", $log_group, $deleteQuery);
					
			}
			
			if($error_count > 0) {
				$apiresults = array("result" => "Error: Delete Failed");
			} else {
				$apiresults = array("result" => "success"); 
			}
			
		}else{
			$query = "SELECT extension  FROM phones $ul ORDER BY extension LIMIT 1;";
			$rsltv = mysqli_query($link,$query);
			
			if($rsltv) {
				while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataExtension = $fresults['extension'];
					$deleteQuery = "DELETE FROM phones WHERE extension = '$dataExtension'"; 
					$deleteResult = mysqli_query($link,$deleteQuery);
					//echo $deleteQuery;
					$deleteQueryB = "DELETE FROM subscriber where username= '$dataExtension'"; 
					$deleteResultB = mysqli_query($linkgokam,$deleteQueryB);
				}
				
				$query = "SELECT extension  FROM phones $ul ORDER BY extension LIMIT 1;";
				$rsltv = mysqli_query($link,$query);
				$countResult = mysqli_num_rows($rsltv);
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
					$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Phone $extension", $log_group, $deleteQuery);
					$apiresults = array("result" => "success");
					
			} else {
				$apiresults = array("result" => "Error: Extension doesn't exist.");
			}
		}
	}//end
?>
