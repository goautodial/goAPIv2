<?php
    #######################################################
    #### Name: goDeleteUserGroup.php	               ####
    #### Description: API to delete specific User Group####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $user_group = $_REQUEST['user_group'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    
    
	if($user_group == null) { 
		$apiresults = array("result" => "Error: Set a value for User Group."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE user_group='$user_group'";
    		} else { 
			$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";  
		}

   		$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataUserGroup = $fresults['user_group'];
			}

			if(!$dataUserGroup == null) {
				$deleteQuery = "DELETE FROM vicidial_user_groups WHERE user_group='$dataUserGroup'"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				$deleteQueryA = "DELETE FROM user_access_group WHERE user_group='$dataUserGroup'";
   				$deleteResultA = mysqli_query($linkgo, $deleteQueryA);
				$apiresults = array("result" => "success");
				
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $deleteQuery);
			} else {
				$apiresults = array("result" => "Error: User Group doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: User Group doesn't exist.");
		}
	}//end
?>
