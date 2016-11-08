<?php
    #######################################################
    #### Name: goGetUserGroupInfo.php	               ####
    #### Description: API to get specific MOH	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $agent_id = $_REQUEST['agent_id'];
    
	if($agent_id == null) { 
		$apiresults = array("result" => "Error: Set a value for AGENT ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE user_group='$agent_id'";
	                $group_type = "Multi-tenant";
    		} else { 
			$ul = "WHERE user_group='$agent_id' AND user_group='$groupId'";  
                	$group_type = "Default";
		}

   		$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);
		
		$queryGL = "SELECT group_level,group_list_id FROM user_access_group WHERE user_group='$agent_id';";
		$rsltvGL = mysqli_query($linkgo, $queryGL);
		$fetchGL = mysqli_fetch_array($rsltvGL);
		
		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataUserGroup[] = $fresults['user_group'];
                $dataGroupName[] = $fresults['group_name'];// .$fresults['dial_method'].$fresults['active'];
                $dataGroupType[] = $group_type;
                $dataForced[] = $fresults['forced_timeclock_login'];
				$dataGroupLevel[] = $fetchGL['group_level'];
				$dataGroupListID[] = $fetchGL['group_list_id'];
                $apiresults = array("result" => "success", "user_group" => $dataUserGroup, "group_name" => $dataGroupName, "group_type" => $dataGroupType, "forced_timeclock_login" => $dataForced, "group_level" => $dataGroupLevel, "group_list_id" => $dataGroupListID);
			}
		} else {
			$apiresults = array("result" => "Error: User Group doesn't exist.");
		}
	}
?>
