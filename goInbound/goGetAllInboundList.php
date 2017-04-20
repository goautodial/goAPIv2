<?php
    #######################################################
    #### Name: goGetAllInboundList.php	               ####
    #### Description: API to get all Inbound Lists     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = mysqli_real_escape_string($link, $_REQUEST['limit']);
    $user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser);
    
    //if (!checkIfTenant($groupId)) {
	if ($user_group == 'ADMIN') {
        $ul='';
    } else { 
		$ul = "WHERE user_group='$user_group'";  
  	}

   $query = "SELECT group_id,group_name,queue_priority,active,call_time_id FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT $limit;";
   $rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_assoc($rsltv)){

	$dataGroupId[] =  $fresults['group_id'];
	$dataGroupName[] =  $fresults['group_name'];
	$dataQueuePriority[] =  $fresults['queue_priority'];
	$dataActive[] =  $fresults['active'];
	$dataCallTimeId[] =  $fresults['call_time_id'];

	$apiresults = array( "result" => "success", "group_id" => $dataGroupId, "group_name" => $dataGroupName, "queue_priority" => $dataQueuePriority, "active" => $dataActive, "call_time_id" => $dataCallTimeId);
	}
?>
