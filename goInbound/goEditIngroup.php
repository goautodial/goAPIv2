<?php
/**
 * @file        goEditInbound.php
 * @brief       API to edit Inbound Details 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James F. Milo  <jerico@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
    
    include_once ("../goFunctions.php");

    // POST or GET Variables
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
	$call_time_id = $_REQUEST['call_time_id'];
	
	// ADVANCED SETTINGS 
		$drop_call_seconds = $_REQUEST['drop_call_seconds'];
		$drop_action = $_REQUEST['drop_action'];
		$drop_exten = $_REQUEST['drop_exten'];
		$voicemail_ext = $_REQUEST['voicemail_ext'];
		$drop_inbound_group = $_REQUEST['drop_inbound_group'];
		$drop_callmenu = $_REQUEST['drop_callmenu'];
		$after_hours_action = $_REQUEST['after_hours_action'];
		$after_hours_voicemail = $_REQUEST['after_hours_voicemail'];
		$after_hours_exten = $_REQUEST['after_hours_exten'];
		$after_hours_message_filename = $_REQUEST['after_hours_message_filename'];
		$after_hours_callmenu = $_REQUEST['after_hours_callmenu'];
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
	
	$log_user = $goUser;
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    // Default values 
    $defActive = array("Y","N");
    $deffronter_display = array("Y","N");
    $defget_call_launch = array('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM','EMAIL');
    $defnext_agent_call = array('fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls');

    if(empty($group_id)) {
        $apiresults = array("result" => "Error: Set a value for Inbound ID.");
    } elseif ( (strlen($group_name) < 2 && !is_null($group_name))  || (strlen($group_color) < 2 && !is_null($group_color)) ) {
		$apiresults = array("result" => "<br>GROUP NOT ADDED - Please go back and look at the data you entered\n <br>Group name and group color must be at least 2 characters in length\n");
	} elseif($queue_priority < -99 || $queue_priority > 99) {
		$apiresults = array("result" => "Error: queue_priority Value should be in between -99 and 99");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
		$apiresults = array("result" => "Error: Special characters found in group_id");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
		$apiresults = array("result" => "Error: Special characters found in group_name");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_color)){
		$apiresults = array("result" => "Error: Special characters found in group_color");
	} elseif(!in_array($active,$defActive) && !is_null($active)) {
	   $apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif(!in_array($fronter_display,$deffronter_display) && !is_null($fronter_display)) {
		$apiresults = array("result" => "Error: Default value for fronter_display is Y or N only.");
	} elseif(!in_array($get_call_launch,$defget_call_launch) && !is_null($get_call_launch)) {
		$apiresults = array("result" => "Error: Default value for get_call_launch is NONE, SCRIPT, WEBFORM, WEBFORMTWO, FORM or EMAIL only.");
	} elseif(!in_array($next_agent_call,$defnext_agent_call) && !is_null($next_agent_call)) {
		$apiresults = array("result" => "Error: Default value for next_agent_call is fewest_calls_campaign, longest_wait_time, ring_all, random, oldest_call_start, oldest_call_finish, overall_user_level, inbound_group_rank, campaign_rank or fewest_calls only.");
	} else {
		$astDB->where("group_id", $group_id);
		$row = $astDB->getValue("vicidial_inbound_groups", "count(*)");
		//$stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
		
		if ($row < 1) {
			$apiresults = array("result" => "GROUP NOT MODIFIED - Inbound doesn't exist");
		} else {
			$data = Array(
					"group_id" => $group_id,
					"group_name" => $group_name,
					"group_color" => $group_color,
					"active" => $active,
					"web_form_address" => $web_form_address,
					"next_agent_call" => $next_agent_call,
					"fronter_display" => $fronter_display,
					"ingroup_script" => $ingroup_script,
					"queue_priority" => $queue_priority,
					"drop_call_seconds" => $drop_call_seconds,
					"drop_action" => $drop_action,
					"drop_exten" => $drop_exten,
					"voicemail_ext" => $voicemail_ext,
					"drop_inbound_group" => $drop_inbound_group,
					"drop_callmenu" => $drop_callmenu,
					"after_hours_action" => $after_hours_action,
					"after_hours_voicemail" => $after_hours_voicemail,
					"after_hours_exten" => $after_hours_exten,
					"get_call_launch" => $get_call_launch,
					"no_agent_no_queue" => $no_agent_no_queue,
					"no_agent_action" => $no_agent_action,
					"welcome_message_filename" => $welcome_message_filename,
					"play_welcome_message" => $play_welcome_message,
					"moh_context" => $moh_context,
					"onhold_prompt_filename" => $onhold_prompt_filename,
					"after_hours_message_filename" => $after_hours_message_filename,
					"call_time_id" => $call_time_id
				);

			// filter for no_agent_action_value
			if(!is_null($no_agents_exten) && $no_agent_action == "MESSAGE"){
				array_push($data, "no_agent_action_value" => $no_agents_exten);
				$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_exten',";
			}
			if(!is_null($no_agents_voicemail) && $no_agent_action == "VOICEMAIL"){
				array_push($data, "no_agent_action_value" => $no_agents_voicemail);
				$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_voicemail',";
			}
			if(!is_null($no_agents_ingroup) && $no_agent_action == "IN_GROUP"){
				array_push($data, "no_agent_action_value" => $no_agents_ingroup);
				$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_ingroup',";
			}
			if(!is_null($no_agents_callmenu) && $no_agent_action == "CALLMENU"){
				array_push($data, "no_agent_action_value" => $no_agents_callmenu);
				$no_agent_action_value_QUERY = "no_agent_action_value = '$no_agents_callmenu',";
			}
			if(!is_null($after_hours_callmenu) && $after_hours_action == "CALLMENU"){
				array_push($data, "after_hours_callmenu" => $after_hours_callmenu);
				$after_hours_callmenu_QUERY = "after_hours_callmenu = '$after_hours_callmenu',";
			}
			
			$astDB->where("group_id", $group_id);
			$updateQuery = $astDB->update("vicidial_inbound_groups", $data);
			// UPDATE ACTION //
			$query = "UPDATE vicidial_inbound_groups
			SET group_id = '$group_id', group_name = '$group_name', group_color = '$group_color', active = '$active', web_form_address = '$web_form_address', next_agent_call = '$next_agent_call', fronter_display = '$fronter_display', ingroup_script = '$ingroup_script', queue_priority = '$queue_priority', drop_call_seconds = '$drop_call_seconds', drop_action = '$drop_action', drop_exten = '$drop_exten', voicemail_ext = '$voicemail_ext', drop_inbound_group = '$drop_inbound_group', drop_callmenu = '$drop_callmenu', after_hours_action = '$after_hours_action', after_hours_voicemail = '$after_hours_voicemail', after_hours_exten = '$after_hours_exten', $after_hours_callmenu_QUERY get_call_launch = '$get_call_launch', no_agent_no_queue = '$no_agent_no_queue', no_agent_action = '$no_agent_action', $no_agent_action_value_QUERY welcome_message_filename = '$welcome_message_filename', play_welcome_message = '$play_welcome_message', moh_context = '$moh_context', onhold_prompt_filename = '$onhold_prompt_filename', after_hours_message_filename = '$after_hours_message_filename', call_time_id = '$call_time_id'
			WHERE group_id='$group_id';";
			
			if($updateQuery){
				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Inbound Group $group_id", $log_group, $query);
				$apiresults = array("result" => "success");
			}else{
				$apiresults = array("result" => "Query Error");
			}
		}
	}
?>
