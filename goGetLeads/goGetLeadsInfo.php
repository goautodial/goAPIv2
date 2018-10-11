<?php
 /**
 * @file 		goGetLeadsInfo.php
 * @brief 		API for Getting Leads Info
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
	
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass'])) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']);
    $lead_id 											= $astDB->escape($_REQUEST['lead_id']);
	$limit 												= (!isset($_REQUEST['limit'])) ? 100 : $astDB->escape($_REQUEST['limit']);
	
	// ERROR CHECKING 
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
	} elseif (empty($lead_id) || is_null($lead_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    } else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {    
			$astDB->where('lead_id', $lead_id);
			$fresults 									= $astDB->getOne('vicidial_list');
			$list_id 									= $fresults['list_id'];
			$is_customer 								= 0;
			
			if ($astDB->count > 0) {
				// check if existing customer
				$goDB->where('lead_id', $lead_id);
				$fresultsc 								= $goDB->getOne('go_customers');
				$is_customer 							= $goDB->getRowCount();
			}
			
			$data 										= empty($fresultsc) ? $fresults : array_merge($fresults, $fresultsc) ;
			
			if (!empty($data)) {
				// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
				// every time we need to filter out requests
				$tenant									= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
					$astDB->orWhere("user_group", "---ALL---");
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($userlevel > 8) {
							$astDB->where("user_group", $log_group);
							$astDB->orWhere("user_group", "---ALL---");
						}
					}					
				}				
				
				$astDB->where("lead_id", $lead_id);
				$astDB->orderBy("call_date", "DESC");
				$vlog_query		 						= $astDB->get("vicidial_log", $limit, "*");
				
				foreach ($vlog_query as $vlog_fetch) {
					$vlog_call_date[] 					= $vlog_fetch['call_date'];
					$vlog_length_in_sec[] 				= gmdate("H:i:s", $vlog_fetch['length_in_sec']);
					$vlog_status[] 						= $vlog_fetch['status'];
					$vlog_user[] 						= $vlog_fetch['user'];
					$vlog_campaign_id[] 				= $vlog_fetch['campaign_id'];
					$vlog_list_id[] 					= $vlog_fetch['list_id'];
					$vlog_term_reason[] 				= $vlog_fetch['term_reason'];
					$vlog_phone_number[] 				= $vlog_fetch['phone_number'];
				}
				
				$vlog_data	 							= array(
					"call_date" 							=> $vlog_call_date, 
					"length_in_sec" 						=> $vlog_length_in_sec, 
					"status" 								=> $vlog_status, 
					"user" 									=> $vlog_user, 
					"campaign_id" 							=> $vlog_campaign_id, 
					"list_id" 								=> $vlog_list_id,
					"term_reason" 							=> $vlog_term_reason, 
					"phone_number" 							=> $vlog_phone_number
				);
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
					$astDB->orWhere("user_group", "---ALL---");
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($userlevel > 8) {
							$astDB->where("user_group", $log_group);
							$astDB->orWhere("user_group", "---ALL---");
						}
					}					
				}
								
				$astDB->where("lead_id", $lead_id);
				$astDB->orderBy("call_date", "DESC");
				$vclog_query		 					= $astDB->get("vicidial_closer_log", $limit, "*");
				
				foreach ($vclog_query as $vclog_fetch) {
					$vclog_call_date[] 					= $vclog_fetch['call_date'];
					$vclog_length_in_sec[] 				= gmdate("H:i:s", $vclog_fetch['length_in_sec']);
					$vclog_status[] 					= $vclog_fetch['status'];
					$vclog_user[] 						= $vclog_fetch['user'];
					$vclog_campaign_id[] 				= $vclog_fetch['campaign_id'];
					$vclog_list_id[] 					= $vclog_fetch['list_id'];
					$vclog_queue_seconds[] 				= $vclog_fetch['queue_seconds'];
					$vclog_term_reason[] 				= $vclog_fetch['term_reason'];
				}
				
				$vclog_data				 				= array(
					"call_date" 							=> $vclog_call_date, 
					"length_in_sec" 						=> $vclog_length_in_sec, 
					"status" 								=> $vclog_status, 
					"user" 									=> $vclog_user, 
					"campaign_id" 							=> $vclog_campaign_id, 
					"list_id" 								=> $vclog_list_id, 
					"queue_seconds" 						=> $vclog_queue_seconds, 
					"term_reason" 							=> $vclog_term_reason
				);
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
					$astDB->orWhere("user_group", "---ALL---");
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($userlevel > 8) {
							$astDB->where("user_group", $log_group);
							$astDB->orWhere("user_group", "---ALL---");
						}
					}					
				}
								
				$astDB->where("lead_id", $lead_id);
				$astDB->orderBy("event_time", "DESC");
				$alog_query		 						= $astDB->get("vicidial_agent_log", $limit, "*");
				
				foreach ($alog_query as $alog_fetch) {
					$alog_event_time[] 					= $alog_fetch['event_time'];
					$alog_campaign_id[] 				= $alog_fetch['campaign_id'];
					$alog_agent_log_id[] 				= $alog_fetch['agent_log_id'];
					$alog_pause_sec[] 					= $alog_fetch['pause_sec'];
					$alog_wait_sec[] 					= $alog_fetch['wait_sec'];
					$alog_talk_sec[] 					= $alog_fetch['talk_sec'];
					$alog_dispo_sec[] 					= $alog_fetch['dispo_sec'];
					$alog_status[] 						= $alog_fetch['status'];
					$alog_user_group[] 					= $alog_fetch['user_group'];
					$alog_sub_status[] 					= $alog_fetch['sub_status'];
				}
				
				$alog_data	 							= array(
					"event_time" 							=> $alog_event_time, 
					"campaign_id" 							=> $alog_campaign_id, 
					"agent_log_id" 							=> $alog_agent_log_id, 
					"pause_sec" 							=> $alog_pause_sec, 
					"wait_sec" 								=> $alog_wait_sec, 
					"talk_sec" 								=> $alog_talk_sec, 
					"dispo_sec" 							=> $alog_dispo_sec, 
					"status" 								=> $alog_status, 
					"user_group" 							=> $alog_user_group, 
					"sub_status" 							=> $alog_sub_status
				);
				
				$rlog_query		 						= $astDB
					->where("lead_id", $lead_id)
					->orderBy("start_time", "DESC")
					->get("recording_log", $limit, "*");
				
				foreach ($rlog_query as $rlog_fetch) {
					$rlog_start_time[] 					= $rlog_fetch['start_time'];
					$rlog_length_in_sec[] 				= gmdate("H:i:s", $rlog_fetch['length_in_sec']);
					$rlog_start_epoch[] 				= $rlog_fetch['start_epoch'];
					$rlog_end_epoch[] 					= $rlog_fetch['end_epoch'];
					$rlog_recording_id[] 				= $rlog_fetch['recording_id'];
					$rlog_filename[] 					= $rlog_fetch['filename'];
					$rlog_location[] 					= $rlog_fetch['location'];
					$rlog_user[] 						= $rlog_fetch['user'];
				}
				
				$rlog_data	 							= array(
					"start_time"							=> $rlog_start_time,
					"start_epoch"							=> $rlog_start_epoch,
					"end_epoch"								=> $rlog_end_epoch,
					"length_in_sec" 						=> $rlog_length_in_sec, 
					"recording_id" 							=> $rlog_recording_id, 
					"filename" 								=> $rlog_filename,
					"location" 								=> $rlog_location, 
					"user" 									=> $rlog_user
				);
				
				$list_id 								= "custom_".$list_id;			
				$cfl_query 								= $astDB->rawQuery("DESC $list_id;");
				
				if ($cfl_query) {
					foreach ($cfl_query as $field_list) {
						$exec_query_CF_list 			= $field_list["Field"];

						if ($exec_query_CF_list != "lead_id") {
							$list_fields[] 				= $exec_query_CF_list;
						}
					}
				}
				
				$fields 								= implode(",", $list_fields);
				
				$cf_query								= $astDB
					->where("lead_id", $lead_id)
					->get($list_id, $limit, $fields);
							
				if ($astDB->count > 0) {
					$CF_fetch 							= $cf_query;

					for ($x=0;$x < count($list_fields);$x++) {
						//if($CF_fetch[$x] !== NULL)
						$CF_data[$list_fields[$x]] 		=  str_replace(",", " | ", $CF_fetch[$x]);
					}
				}

				$apiresults 							= array(
					"result" 								=> "success", 
					"data" 									=> $data, 
					"is_customer" 							=> $is_customer, 
					"calls" 								=> $vlog_data, 
					"closerlog" 							=> $vclog_data, 
					"agentlog" 								=> $alog_data, 
					"record" 								=> $rlog_data,
					"custom_fields" 						=> $CF_data
				);			
			} else {
				$err_msg 								= error_handle("41004", "lead_id");
				$apiresults 							= array(
					"code" 									=> "41004",
					"result" 								=> $err_msg
				);
			}
			
			$log_id 									= log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the lead info of Lead ID: $lead_id", $log_group);
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
