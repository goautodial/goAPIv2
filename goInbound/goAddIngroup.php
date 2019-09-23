<?php
/**
 * @file        goAddInbound.php
 * @brief       API to add new Inbound
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian V Samatra  <jeremiah@goautodial.com>
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
    
    include_once ("goAPI.php");

	// POST or GET Variables
    $group_id 									= $astDB->escape($_REQUEST['group_id']);
    $group_name 								= $astDB->escape($_REQUEST['group_name']);
    $group_color 								= $astDB->escape($_REQUEST['group_color']);
    $active 									= $astDB->escape($_REQUEST['active']);
    $web_form_address 							= $astDB->escape($_REQUEST['web_form_address']);
    $voicemail_ext 								= $astDB->escape($_REQUEST['voicemail_ext']);
    $next_agent_call 							= $astDB->escape($_REQUEST['next_agent_call']);
    $fronter_display 							= $astDB->escape($_REQUEST['fronter_display']);
    $ingroup_script 							= $astDB->escape($_REQUEST['ingroup_script']);
    $get_call_launch 							= $astDB->escape($_REQUEST['get_call_launch']);
    // $web_form_address_two 					= "";
    // $start_call_url 							= "";
    // $dispo_call_url 							= "";
    // $add_lead_url 							= "";
    // $uniqueid_status_prefix 							= "";
    // $call_time_id 							= "";
    $user_group 								= $_REQUEST['user_group'];


    // Default values 
    $defActive 									= array("Y","N");
    $deffronter_display 						= array("Y","N");
    
    $defget_call_launch 						= array(
		'NONE',
		'SCRIPT',
		'WEBFORM',
		'WEBFORMTWO',
		'FORM',
		'EMAIL'
	);
	
    $defnext_agent_call 						= array(
		'fewest_calls_campaign',
		'longest_wait_time',
		'ring_all',
		'random',
		'oldest_call_start',
		'oldest_call_finish',
		'overall_user_level',
		'inbound_group_rank',
		'campaign_rank',
		'fewest_calls'
	);

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($group_id)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for Group ID."
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)) {
		$apiresults 							= array(
			"result" 								=> "Error: Special characters found in group_id"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || empty($group_name)) {
		$apiresults 							= array(
			"result" 								=> "Error: Special characters found in group_name and must not be empty"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_color) || empty($group_color)) {
		$apiresults 							= array(
			"result" 								=> "Error: Special characters found in group_color must not be empty"
		);
	} elseif (!in_array($active,$defActive)) {
		$apiresults 							= array(
			"result" 								=> "Error: Default value for active is Y or N only."
		);
	} elseif (!in_array($fronter_display,$deffronter_display)) {
		$apiresults 							= array(
			"result" 								=> "Error: Default value for fronter_display is Y or N only."
		);
	} elseif (!in_array($get_call_launch,$defget_call_launch)) {
		$apiresults 							= array(
			"result" 								=> "Error: Default value for get_call_launch is NONE, SCRIPT, WEBFORM, WEBFORMTWO, FORM or EMAIL only."
		);
	} elseif (!in_array($next_agent_call,$defnext_agent_call)) {
		$apiresults 							= array(
			"result" 								=> "Error: Default value for next_agent_call is fewest_calls_campaign, longest_wait_time, ring_all, random, oldest_call_start, oldest_call_finish, overall_user_level, inbound_group_rank, campaign_rank or fewest_calls only."
		);
	} else {
		if (checkIfTenant ($log_group, $goDB)) {
			$astDB->where ("user_group", $log_group);
		}
		
		$astDB->getOne("vicidial_user_groups");

		if ($astDB->count > 0) {
			$astDB->where("id_table", "vicidial_inbound_groups");
			$astDB->where("active", 1);
			$voi_ct 							= $astDB->getValue("vicidial_override_ids", "count(*)");
			
			if ($voi_ct > 0) {
				$datum 							= Array(
					"value" 						=> $group_id
				);
				
				$astDB->where("id_table", "vicidial_inbound_groups");
				$astDB->where("active", 1);
				$astDB->update("vicidial_override_ids", $datum);
			}

			$astDB->where("group_id", $group_id);
			$row 								= $astDB->getValue("vicidial_inbound_groups", "count(*)");
			
			if ($row > 0) {
				$apiresults 					= array(
					"result" 						=> "GROUP NOT ADDED - there is already a Inbound in the system with this ID\n"
				);
			} else {
				$astDB->where("campaign_id", $group_id);
				$count 							= $astDB->getValue("vicidial_campaigns", "count(*)");
				
				if ($count > 0) {
					$apiresults 				= array(
						"result" 					=> "<br>GROUP NOT ADDED - there is already a campaign in the system with this ID\n"
					);
				} else {
					if ( (strlen($group_id) < 2) || (strlen($group_name) < 2)  || (strlen($group_color) < 2) || (strlen($group_id) > 20) || (preg_match('/ /i',$group_id)) or (preg_match("/\-/i",$group_id)) || (preg_match("/\+/i",$group_id)) ) {
						$apiresults 			= array(
							"result" 				=> "<br>GROUP NOT ADDED - Please go back and look at the data you entered\n <br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n <br>Group name and group color must be at least 2 characters in length\n"
						);
					} else {
						$col 					= Array(
							"group_id" 				=> $group_id,
							"group_name" 			=> $group_name,
							"group_color" 			=> $group_color,
							"active" 				=> $active,
							"web_form_address" 		=> $web_form_address,
							"voicemail_ext" 		=> $voicemail_ext,
							"next_agent_call" 		=> $next_agent_call,
							"fronter_display" 		=> $fronter_display,
							"ingroup_script" 		=> $ingroup_script,
							"get_call_launch" 		=> $get_call_launch,
							"web_form_address_two" 	=> "",
							"start_call_url" 		=> "",
							"dispo_call_url" 		=> "",
							"add_lead_url" 			=> "",
							"uniqueid_status_prefix" => $accounts,
							"call_time_id" 			=> "24hours",
							"user_group" 			=> $user_group
						);
						$astDB->insert("vicidial_inbound_groups", $col);
						//$stmtInsert 			= "INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address_two,start_call_url,dispo_call_url,add_lead_url,uniqueid_status_prefix,call_time_id,user_group) values('$group_id','$group_name','$group_color','$active','$web_form_address','$voicemail_ext','$next_agent_call','$fronter_display','$script_id','$get_call_launch','','','','','$accounts','24hours','$user_group');";
						//$query 							= mysqli_query($link, $stmtInsert);

						$astDB->where("group_id", $group_id);
						$countAdd 				= $astDB->getValue("vicidial_inbound_groups", "count(*)");
						//$resultQueryAddCheck 							= "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
						
						$datum 					= Array(
							"campaign_id" 			=> $group_id
						);
						
						$astDB->insert("vicidial_campaign_stats", $datum);
						//$stmtA="INSERT INTO vicidial_campaign_stats (campaign_id) values('$group_id');";
						
						$astDB->where("campaign_id", $group_id);
						$countAdd1 				= $astDB->getValue("vicidial_campaign_stats", "count(*)");
						//$resultQueryAddCheck1 							= "SELECT campaign_id from vicidial_campaign_stats where campaign_id='$group_id';";
						
						if ($countAdd1 > 0 && $countAdd > 0) {
							$log_id 			= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Group $group_id", $log_group, $stmtInsert);
							$apiresults 		= array(
								"result" 			=> "success"
							);
						} else {
							$apiresults 		= array(
								"result" 			=> "GROUP NOT ADDED - Check the name and value you type\n"
							);
						}
					}
				}
			}

		} else  {
			$apiresults 						= array(
				"result" 							=> "INVALID User Group"
			);
		}
	}
	
?>
