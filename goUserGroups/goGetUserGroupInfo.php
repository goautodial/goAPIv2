<?php
    #######################################################
    #### Name: goGetUserGroupInfo.php	               ####
    #### Description: API to get specific MOH	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    include_once("../goDBgoautodial.php");
	
    ### POST or GET Variables
    $agent_id = $_REQUEST['user_group'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    
	if($agent_id == null) { 
		$apiresults = array("result" => "Error: Set a value for User Group."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE user_group='$agent_id'";
	                $group_type = "Multi-tenant";
    		} else { 
			$ul = "WHERE user_group='$agent_id' AND user_group='$groupId'";  
                	$group_type = "Default";
		}

   		$query = "SELECT user_group,group_name,forced_timeclock_login,shift_enforcement,allowed_campaigns FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);
		$rsltv = mysqli_fetch_assoc($rsltv);
		
		$queryGL = "SELECT group_level,permissions FROM user_access_group WHERE user_group='$agent_id';";
		$rsltvGL = mysqli_query($linkgo, $queryGL);
		$fetchGL = mysqli_fetch_assoc($rsltvGL);
		
		$data = array_merge($rsltv, $fetchGL);
		
		$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the info of User Group: $agent_id", $log_group);
		//if(!empty($data)) {
            $apiresults = array("result" => "success", "data" => $data);
		/*} else {
			$apiresults = array("result" => "Error: User Group doesn't exist.");
		}*/
	}
?>
