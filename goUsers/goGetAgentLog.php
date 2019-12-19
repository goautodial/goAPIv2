<?php
/**
 * @file 		goGetAgentLog.php
 * @brief 		API to get agent log of user
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja
 * @author		Demian Lizandro A. Biscocho 
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
**/

    include_once ("goAPI.php");
        
    // POST or GET Variables
    $user 												= $astDB->escape($_REQUEST['user']);
    $start_date 										= $astDB->escape($_REQUEST['start_date']);
	$end_date 											= $astDB->escape($_REQUEST['end_date']);
	$agentlog 											= $astDB->escape($_REQUEST['agentlog']);
	
    // Check user_id if its null or empty
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($user)) {
		$err_msg 										= error_handle("40002");
		$apiresults 									= array(
			"code" 											=> "40002", 
			"result" 										=> $err_msg
		);
        //$apiresults = array("result" => "Error: Set a value for User ID.");        
    } else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}
		
			$limit 										= "5000";
			$date 										= date ("Y-m-d");
			
			if (!empty($start_date)) {
				$start_date 							= $start_date . " 00:00:00";
				$end_date 								= $end_date . " 23:59:59";
			} else {
				$start_date 							= $date . "00:00:00";
				$end_date 								= $date . "23:59:59";
			}
						
			$astDB->where("user", $user);
			$astDB->where("date_format(call_date, '%Y-%m-%d %H:%i:%s')", array($start_date, $end_date), "BETWEEN");
			$outbound_query  							= $astDB->get("vicidial_log", $limit);
			
			if ($astDB->count > 0) {		
				foreach ($outbound_query as $outbound_fetch) {
					$agent_user[] 						= $outbound_fetch['user'];
					$event_time[] 						= $outbound_fetch['call_date'];
					$status[] 							= $outbound_fetch['status'];
					$phone_number[] 					= $outbound_fetch['phone_number'];
					$campaign_id[] 						= $outbound_fetch['campaign_id'];
					$user_group[] 						= $outbound_fetch['user_group'];
					$list_id[] 							= $outbound_fetch['list_id'];
					$lead_id[] 							= $outbound_fetch['lead_id'];
					$term_reason[] 						= $outbound_fetch['term_reason'];
				}
				
				$outbound_array 						= array(
					"user" 									=> $agent_user, 
					"call_date" 							=> $event_time, 
					"status" 								=> $status, 
					"phone_number" 							=> $phone_number, 
					"campaign_id" 							=> $campaign_id, 
					"user_group" 							=> $user_group, 
					"list_id" 								=> $list_id, 
					"lead_id" 								=> $lead_id, 
					"term_reason" 							=> $term_reason
				);
			}
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {        
				if (strtoupper ($log_group) != 'ADMIN') {
					if ($user_level > 8) {
						$astDB->where("user_group", $log_group);
					}
				}
			}
			
			$closerlog_query  							= $astDB
				->where("user", $user)
				->where("date_format(call_date, '%Y-%m-%d %H:%i:%s')", array($start_date, $end_date), "BETWEEN")			
				->get("vicidial_closer_log", $limit);
			
			if ($astDB->count > 0) {
				foreach ($closerlog_query as $closerlog_fetch) {
					$closerlog_call_date[] 				= $closerlog_fetch['call_date'];
					$closerlog_length_in_sec[] 			= gmdate("H:i:s", $closerlog_fetch['length_in_sec']);
					$closerlog_status[] 				= $closerlog_fetch['status'];
					$closerlog_user[] 					= $closerlog_fetch['user'];
					$closerlog_user_group[] 			= $closerlog_fetch['user_group'];
					$closerlog_campaign_id[] 			= $closerlog_fetch['campaign_id'];
					$closerlog_list_id[] 				= $closerlog_fetch['list_id'];
					$closerlog_queue_seconds[] 			= $closerlog_fetch['queue_seconds'];
					$closerlog_term_reason[] 			= $closerlog_fetch['term_reason'];
					$closerlog_phone_number[] 			= $closerlog_fetch['phone_number'];
				}
				
				$closerlog_array 						= array(
					"call_date" 							=> $closerlog_call_date, 
					"length_in_sec" 						=> $closerlog_length_in_sec, 
					"status" 								=> $closerlog_status, 
					"user_group" 							=> $closerlog_user_group, 
					"campaign_id" 							=> $closerlog_campaign_id, 
					"list_id" 								=> $closerlog_list_id, 
					"queue_seconds" 						=> $closerlog_queue_seconds, 
					"term_reason" 							=> $closerlog_term_reason,
					"phone_number"							=> $closerlog_phone_number
				);
			}
			
			$query = "
				SELECT agent_log_id, user, sub_status, event_time, campaign_id, user_group FROM vicidial_agent_log 
				WHERE user = '$user' AND sub_status IS NOT NULL AND (date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date') 
			UNION 
				SELECT user_log_id, user, event, event_date as event_time, campaign_id, user_group FROM vicidial_user_log 
				WHERE user = '$user' AND (date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date') 
				ORDER BY event_time DESC LIMIT $limit 
			";
			
			$userlog_query 								= $astDB->rawQuery($query);
			//$rowCount 								= $astDB->getRowCount();
			
			if ($userlog_query > 0) {
				foreach ($userlog_query as $userlog_fetch) {
					$userlog_log_id[] 					= $userlog_fetch['agent_log_id'];
					$userlog_user[] 					= $userlog_fetch['user'];
					$userlog_event[] 					= $userlog_fetch['sub_status'];
					$userlog_event_date[] 				= $userlog_fetch['event_time'];
					$userlog_campaign_id[] 				= $userlog_fetch['campaign_id'];
					$userlog_user_group[] 				= $userlog_fetch['user_group'];
					//$userlog_lead_id[] 				= $userlog_fetch['lead_id'];
					//$userlog_comments[] 				= $userlog_fetch['comments'];
				}
				
				$userlog_array 							= array(
					"agent_log_id" 							=> $userlog_log_id, 
					"user" 									=> $userlog_user, 
					"sub_status" 							=> $userlog_event, 
					"event_time" 							=> $userlog_event_date, 
					"campaign_id" 							=> $userlog_campaign_id, 
					"user_group" 							=> $userlog_user_group
					//"lead_id"								=> $userlog_lead_id,
					//"comments"							=> $userlog_comments
				);
			}
			
			if ($agentlog == "outbound") {
				$data									= $outbound_array;
			} elseif ($agentlog == "inbound") {
				$data									= $inbound_array;
			} elseif ($agentlog == "userlog") {
				$data									= $userlog_array;
			}
			
			$apiresults 								= array(
				"result" 									=> "success", 
				"data"	 									=> $data
			);			
			//$log_id 									= log_action($goDB, 'VIEW', $user, $log_ip, "Viewed the agent log of Agent: $user", $log_group);
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>

