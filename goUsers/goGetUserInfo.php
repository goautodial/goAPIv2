<?php
/**
 * @file        goGetUserInfo.php
 * @brief       API to get specific user details 
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho
 * @author      Alexander Jim H. Abenoja
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
    
	if(isset($_REQUEST['filter'])){
		$filter 											= $astDB->escape($_REQUEST['filter']);
	} else { 
		$filter                                                                                         = "default";
	}
	
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($_REQUEST['user_id']) && empty($_REQUEST['user'])) {
		$err_msg 										= error_handle("40002");
		$apiresults 									= array(
			"code" 											=> "40002", 
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
			if (isset($_REQUEST['user_id'])  && isset($_REQUEST['user'])) {
				$user_id 								= $astDB->escape($_REQUEST['user_id']);
				$user 									= $astDB->escape($_REQUEST['user']);
			} 
			
			if (isset($_REQUEST['user_id'])  && !isset($_REQUEST['user'])) {
				$user_id 								= $astDB->escape($_REQUEST['user_id']);
			} 
			
			if (!isset($_REQUEST['user_id'])  && isset($_REQUEST['user'])) {
				$user									= $astDB->escape($_REQUEST['user']);
			}
			
			if (!empty($user_id) && empty($user)) { // Am I (agent/user) alive in whole system?
				$astDB->where("user_id", $user_id);
			} else {
				$astDB->where("user", $user);			
			}
					
			$userinfo									= $astDB->getOne("vicidial_users", "user_id, user");
			$user_id									= $userinfo["user_id"];
			$user										= $userinfo["user"];
			
			if ($astDB->count > 0) { // Yes, I am!					
				// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
				// every time we need to filter out requests
				$tenant									= (checkIfTenant($log_group, $goDB) ? 1 : 0);
				
				if ($tenant) { // I have some conditions for you..
					$astDB->where("user_group", $log_group); 
				} elseif (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}				
						
				$userinfo								= $astDB // Are you powerful enough to access me?
					->where("user", $user)
					->where("user", DEFAULT_USERS, "NOT IN")
					->getOne("vicidial_users"); 
						
				if ($astDB->count > 0) { // Yes, you are!
					$check_location 					= go_check_user_location(NULL, $user_id);
					
					if ($check_location !== 0) {
						$usergo							= $goDB
							->where("us.name", $user)
							->where("us.location_id = lo.id")
							->getOne("users us, locations lo");					
					} else {
						$usergo							= $goDB
							->where("name", $user)
							->getOne("users"); 
					}
					
					$avatar								= $goDB
						->where("user_id", $user_id)
						->getOne("go_avatars", "data");
					
					$onlineako 							= $astDB // Am I logged-in and online?
						->where("user", $user)
						->getOne("vicidial_live_agents", "user,status");
					
					$datetoday 							=  date ("Y-m-d");
					$status								= "SALE";
					
					if ($astDB->count > 0) { // Yes, I am!							
						$incallstoday					= $astDB
							->where("vu.user = $user")
							->where("vcl.user = vu.user")
							->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
							->where("vcl.uniqueid = cl.uniqueid")
							->where("vcl.length_in_sec", 0, ">")
							->getValue("vicidial_closer_log as vcl, vicidial_users as vu, call_log as cl", "count(vcl.lead_id)");
							
						$incallstoday 					= ($incallstoday) ? $incallstoday : "";
															
						$insalestoday					= $astDB			
							->where("vu.user = $user")
							->where("vcl.user = vu.user")
							->where("vcl.status", $status)
							->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")					
							->getValue("vicidial_closer_log as vcl, vicidial_users as vu", "count(distinct lead_id)");					
						
						$insalestoday 					= ($insalestoday) ? $insalestoday : "";
						
						$outcallstoday					= $astDB
							->where("vu.user = $user")
							->where("vl.user = vu.user")
							->where("vl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
							->where("vl.uniqueid = cl.uniqueid")
							->where("vl.length_in_sec", 0, ">")
							->getValue("vicidial_log as vl, vicidial_users as vu, call_log as cl", "count(vl.lead_id)");

						$outcallstoday 					= ($outcallstoday) ? $outcallstoday : "";
										
						$outsalestoday					= $astDB				
							->where("vu.user = $user")
							->where("vl.user = vu.user")
							->where("vl.status", $status)
							->where("vl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")					
							->getValue("vicidial_log as vl, vicidial_users as vu", "count(distinct lead_id)");
						
						$outsalestoday					= ($outsalestoday) ? $outsalestoday : "";
						
						$cols 							= array(
							"vicidial_live_agents.extension as vla_extension",
							"vicidial_live_agents.user as vla_user",
							"vicidial_users.full_name as vu_full_name",
							"vicidial_users.user_group as vu_user_group",
							"vicidial_users.phone_login as vu_phone_login",
							"vicidial_live_agents.conf_exten as vla_conf_exten",
							"vicidial_live_agents.status as vla_status",
							"vicidial_live_agents.comments as vla_comments",
							"vicidial_live_agents.server_ip as vla_server_ip",
							"vicidial_live_agents.call_server_ip as vla_call_server_ip",
							"UNIX_TIMESTAMP(last_call_time) as last_call_time",
							"UNIX_TIMESTAMP(last_call_finish) as last_call_finish",
							"vicidial_live_agents.campaign_id as vla_campaign_id",
							"UNIX_TIMESTAMP(last_state_change) as last_state_change",
							"vicidial_live_agents.lead_id as vla_lead_id",
							"vicidial_live_agents.agent_log_id as vla_agent_log_id",
							"vicidial_users.user_id as vu_user_id",
							"vicidial_users.user as vu_user",
							"vicidial_live_agents.callerid as vla_callerid",
							"vicidial_agent_log.sub_status as vla_pausecode", 
							"vicidial_campaigns.campaign_name as vla_campaign_name"
						);
						
						if (strstr("/READY|CLOSER/", $onlineako["status"])) { // I'm waiting for calls..
							$table						= "
								vicidial_live_agents,
								vicidial_users,
								vicidial_agent_log,
								vicidial_campaigns
							";	
							
							$yesnocalls					= $astDB
								->where("vicidial_live_agents.user", $user)
								->where("vicidial_live_agents.campaign_id = vicidial_campaigns.campaign_id")
								->where("vicidial_live_agents.user = vicidial_users.user")
								->where("vicidial_live_agents.lead_id = 0")
								->where("vicidial_live_agents.user_level != 4")
								->where("vicidial_live_agents.agent_log_id = vicidial_agent_log.agent_log_id")
								->orderBy ("last_call_time")		
								->get($table, null, $cols);
						}
						
						if (strstr("/PAUSED/", $onlineako["status"])) { // I'm waiting for calls..
							$table						= "
								vicidial_live_agents,
								vicidial_users,
								vicidial_agent_log,
								vicidial_campaigns
							";	
							
							$yesnocalls					= $astDB
								->where("vicidial_live_agents.user", $user)
								->where("vicidial_live_agents.campaign_id = vicidial_campaigns.campaign_id")
								->where("vicidial_live_agents.user = vicidial_users.user")
								->where("vicidial_live_agents.user_level != 4")
								->where("vicidial_live_agents.agent_log_id = vicidial_agent_log.agent_log_id")
								->orderBy ("last_call_time")		
								->get($table, null, $cols);
						}
						
						if (strstr ("/INCALL|QUEUE|PARK|3-WAY/", $onlineako["status"])) { // I'm on a call..
							$table						= "
								vicidial_live_agents,
								vicidial_users,
								vicidial_list,
								vicidial_agent_log,
								vicidial_campaigns
							";
							
							$yesnocalls					= $astDB
								->where("vicidial_live_agents.user", $user)
								->where("vicidial_live_agents.campaign_id = vicidial_campaigns.campaign_id")
								->where("vicidial_live_agents.user = vicidial_users.user")
								->where("vicidial_live_agents.lead_id = vicidial_list.lead_id")
								->where("vicidial_live_agents.user_level != 4")
								->where("vicidial_live_agents.agent_log_id = vicidial_agent_log.agent_log_id")
								->orderBy ("last_call_time")		
									->get($table, null, $cols);	
									
							foreach ($yesnocalls as $yescalls) {
								$callerid 				= $yescalls['vla_callerid'];								                    
							}
							
							$callerids					= $astDB
								->where("callerid", $callerid)
								->get("vicidial_auto_calls");
									
							$parked						= $astDB
								->where("channel_group", $callerid)
								->get("parked_channels");			
						}
						
						$salestoday						= array(
							"insalestoday" 					=> $insalestoday, 
							"outsalestoday" 				=> $outsalestoday 
						);
						
						$callstoday						= array(
							"incallstoday" 					=> $incallstoday, 
							"outcallstoday" 				=> $outcallstoday 
						);
						
						$onlinedata						= array_merge($yesnocalls, $salestoday, $callstoday);
						
						$apiresults 					= array(
							"result" 						=> "success", 
							"data" 							=> $onlinedata,
							"dataGo" 						=> $usergo,
							"avatar"						=> $avatar,
							"parked" 						=> $parked, 
							"callerids" 					=> $callerids				
						);								
					} else { // I'm offline.
						if (!empty($usergo)) {
							$data 						= array_merge($userinfo, $usergo);
						} else {
							$data 						= $userinfo;
						}	
						
						$apiresults 					= array(// Since I'm offline, here's what you get..
							"result" 						=> "success", 
							"data" 							=> $data,
							"avatar"						=> $avatar
						);					
					}
					
					if ($filter == "userInfo") { // Oh.. you know me..
						if (!empty($usergo)) {
							$data 						= array_merge($userinfo, $usergo);
						} else {
							$data 						= $userinfo;
						}	
						
						$apiresults 					= array(
							"result" 						=> "success", 
							"data" 							=> $data
						);            
					}            
				} else { // No, you're not powerful enough!
					$err_msg 							= error_handle("10001", "Insufficient permission.");
					$apiresults							= array(
						"code" 								=> "10001",
						"result" 							=> $err_msg
					);		
				}
			} else { // No, I'm not alive. :(		
				$err_msg 								= error_handle("41004", "user. Doesn't exist");
				$apiresults								= array(
					"code" 									=> "41004",
					"result" 								=> $err_msg
				);
			}
		}
	}

?>
