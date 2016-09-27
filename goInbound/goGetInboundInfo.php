<?php
    #######################################################
    #### Name: getCampaignInfo.php	               ####
    #### Description: API to get specific campaign     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
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

   		$query = "SELECT group_id, group_name, group_color, active, web_form_address, next_agent_call, queue_priority, fronter_display, ingroup_script, drop_call_seconds, drop_action, drop_exten, drop_inbound_group, drop_callmenu, voicemail_ext, call_time_id, after_hours_action, after_hours_message_filename, after_hours_exten, after_hours_voicemail, get_call_launch, no_agent_no_queue, no_agent_action, no_agent_action_value, welcome_message_filename, play_welcome_message, moh_context, onhold_prompt_filename FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			$fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
		
			$apiresults = array( "result" => "success", "data" => $fresults);

		} else {
			$apiresults = array("result" => "Error: Inbound doesn't exist.");
		}
	}
?>
