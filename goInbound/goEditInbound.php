<?php
   ####################################################
   #### Name: goEditInbound.php                    ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
    ### POST or GET Variables
        $group_id = $_REQUEST['group_id'];
        $group_name = $_REQUEST['group_name'];
        $group_color = $_REQUEST['group_color'];
        $active = $_REQUEST['active'];
        $web_form_address = $_REQUEST['web_form_address'];
        $next_agent_call = $_REQUEST['next_agent_call'];
        $fronter_display = $_REQUEST['fronter_display'];
        $ingroup_script = $_REQUEST['ingroup_script'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
		$queue_priority = $_REQUEST['queue_priority'];
		
		### ADVANCED SETTINGS ##
		$drop_call_seconds = $_REQUEST['drop_call_seconds'];
		$drop_action = $_REQUEST['drop_action'];
		$drop_exten = $_REQUEST['drop_exten'];
		$voicemail_ext = $_REQUEST['voicemail_ext'];
		$drop_inbound_group = $_REQUEST['drop_inbound_group'];
		$drop_callmenu = $_REQUEST['drop_callmenu'];
		$after_hours_action = $_REQUEST['after_hours_action'];
		$after_hours_voicemail = $_REQUEST['after_hours_voicemail'];
		$after_hours_exten = $_REQUEST['after_hours_exten'];
		//afterhours_xfer_group = $_REQUEST['afterhours_xfer_group'];
		$get_call_launch = $_REQUEST['get_call_launch'];
		$no_agent_no_queue = $_REQUEST['no_agent_no_queue'];
		$no_agent_action = $_REQUEST['no_agent_action'];
		$no_agents_exten = $_REQUEST['no_agents_exten'];
		$no_agents_voicemail = $_REQUEST['no_agents_voicemail'];
		$no_agents_ingroup = $_REQUEST['no_agents_ingroup'];
		$no_agents_callmenu = $_REQUEST['no_agents_callmenu'];
		$welcome_message_filename = $_REQUEST['welcome_message_filename'];
		$play_welcome_message = $_REQUEST['play_welcome_message'];
		$moh_context = $_REQUEST['moh_context'];
		$onhold_prompt_filename = $_REQUEST['onhold_prompt_filename'];
		
	//$values = $_REQUEST['items'];
 //group_id, group_name, group_color, active, web_form_address, next_agent_call, fronter_display, ingroup_script, queue_priority

    ### Default values 
    $defActive = array("Y","N");
    $deffronter_display = array("Y","N");
    $defget_call_launch = array('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM','EMAIL');
    $defnext_agent_call = array('fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls');

####################################


/* start lists */

        if($group_id == null) {
                $apiresults = array("result" => "Error: Set a value for Inbound ID.");
        } else {
				if ( (strlen($group_name) < 2 && $group_name != null)  || (strlen($group_color) < 2 && $group_color != null) ) {
					 $apiresults = array("result" => "<br>GROUP NOT ADDED - Please go back and look at the data you entered\n <br>Group name and group color must be at least 2 characters in length\n");
				} else {
						if($queue_priority < -99 || $queue_priority > 99) {
								$apiresults = array("result" => "Error: queue_priority Value should be in between -99 and 99");
						} else {
								if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
										$apiresults = array("result" => "Error: Special characters found in group_id");
								} else {
										if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
												$apiresults = array("result" => "Error: Special characters found in group_name");
										} else {
												if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_color)){
														$apiresults = array("result" => "Error: Special characters found in group_color");
												} else {
														if(!in_array($active,$defActive) && $active != null) {
																	   $apiresults = array("result" => "Error: Default value for active is Y or N only.");
														} else {
																if(!in_array($fronter_display,$deffronter_display) && $fronter_display != null) {
																		$apiresults = array("result" => "Error: Default value for fronter_display is Y or N only.");
																} else {
																		if(!in_array($get_call_launch,$defget_call_launch) && $get_call_launch != null) {
																				$apiresults = array("result" => "Error: Default value for get_call_launch is NONE, SCRIPT, WEBFORM, WEBFORMTWO, FORM or EMAIL only.");
																		} else {
																				if(!in_array($next_agent_call,$defnext_agent_call) && $next_agent_call != null) {
																						$apiresults = array("result" => "Error: Default value for next_agent_call is fewest_calls_campaign, longest_wait_time, ring_all, random, oldest_call_start, oldest_call_finish, overall_user_level, inbound_group_rank, campaign_rank or fewest_calls only.");
																				} else {


																						$stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
																						$queryCheck =  mysqli_query($link, $stmtCheck);
																						$row = mysqli_num_rows($queryCheck);
		  
																						if ($row <= 0) {
																								$apiresults = array("result" => "GROUP NOT MODIFIED - Inbound doesn't exist");
																						} else {
						
																								# filter for no_agent_action_value
																								if($no_agents_exten != NULL && $no_agent_action == "MESSAGE"){
																										$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_exten',";
																								}
																								if($no_agents_voicemail != NULL && $no_agent_action == "VOICEMAIL"){
																										$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_voicemail',";
																								}
																								if($no_agents_ingroup != NULL && $no_agent_action == "IN_GROUP"){
																										$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_ingroup',";
																								}
																								if($no_agents_callmenu != NULL && $no_agent_action == "CALLMENU"){
																										$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_callmenu',";
																								}
																								
																								### UPDATE ACTION ###
																								$query = "UPDATE vicidial_inbound_groups
																										SET group_id = '$group_id', group_name = '$group_name', group_color = '$group_color', active = '$active', web_form_address = '$web_form_address', next_agent_call = '$next_agent_call', fronter_display = '$fronter_display', ingroup_script = '$ingroup_script', queue_priority = '$queue_priority', drop_call_seconds = '$drop_call_seconds', drop_action = '$drop_action', drop_exten = '$drop_exten', voicemail_ext = '$voicemail_ext', drop_inbound_group = '$drop_inbound_group', drop_callmenu = '$drop_callmenu', after_hours_action = '$after_hours_action', after_hours_voicemail = '$after_hours_voicemail', after_hours_exten = '$after_hours_exten', get_call_launch = '$get_call_launch', no_agent_no_queue = '$no_agent_no_queue', no_agent_action = '$no_agent_action', $no_agent_action_value_QUERY welcome_message_filename = '$welcome_message_filename', play_welcome_message = '$play_welcome_message', moh_context = '$moh_context', onhold_prompt_filename = '$onhold_prompt_filename' WHERE group_id='$group_id';";
																								$resultQuery = mysqli_query($link, $query);

				
																								### Admin logs
																										$SQLdate = date("Y-m-d H:i:s");
																										$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY IN_GROUP $group_id','UPDATE vicidial_inbound_groups SET group_id=$group_id, group_name=$group_name, group_color=$group_color, active=$active, web_form_address=$web_form_address, next_agent_call=$next_agent_call, fronter_display=$fronter_display, ingroup_script=$ingroup_script, queue_priority=$queue_priority WHERE group_id=$groupid_data;');";
																								$rsltvLog = mysqli_query($linkgo, $queryLog);
		
																								if($resultQuery){
																										$apiresults = array("result" => "success");
																								}else{
																										$apiresults = array("result" => $query);
																								}
																								
																								
																						}
																				}
																		}
																}
														}
												}
										}
								}
						}
				}
		}

?>
