<?php
    #######################################################
    #### Name: getCampaignInfo.php	               ####
    #### Description: API to get specific campaign     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
    $group_id = $_REQUEST['group_id'];
    
	if($group_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Group ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE group_id='$group_id'";
    		} else { 
			$ul = "WHERE group_id='$group_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT group_id,group_name,queue_priority,active,call_time_id,group_color FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
        $dataGroupId[] =  $fresults['group_id'];
        $dataGroupName[] =  $fresults['group_name'];
        $dataQueuePriority[] =  $fresults['queue_priority'];
        $dataActive[] =  $fresults['active'];
        $dataCallTimeId[] =  $fresults['call_time_id'];
		$dataGroupColor[] =  $fresults['group_color'];
		
    $apiresults = array( "result" => "success", "group_id" => $dataGroupId, "group_name" => $dataGroupName, "queue_priority" => $dataQueuePriority, "active" => $dataActive, "call_time_id" => $dataCallTimeId, "group_color" => $dataGroupColor);

			}
		} else {
			$apiresults = array("result" => "Error: Inbound doesn't exist.");
		}
	}
?>
