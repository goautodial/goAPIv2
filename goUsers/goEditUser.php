<?php
/**
 * @file 		goEditUser.php
 * @brief 		API to edit specific User Details
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
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
    $userid 											= $astDB->escape($_REQUEST['user_id']);
    $user 												= $astDB->escape($_REQUEST['user']);
    $pass 												= $astDB->escape($_REQUEST['pass']);
    $full_name 											= $astDB->escape($_REQUEST['full_name']);
    $phone_login 										= $astDB->escape($_REQUEST['phone_login']);
    $phone_pass 										= $pass;
    $user_group 										= $astDB->escape($_REQUEST['user_group']);
    $email 												= $astDB->escape($_REQUEST['email']);
    $active 											= $astDB->escape(strtoupper($_REQUEST['active']));
    $hotkeys_active 									= $astDB->escape($_REQUEST['hotkeys_active']);
    $user_level 										= $astDB->escape($_REQUEST['user_level']);
    $modify_same_user_level 							= $astDB->escape(strtoupper($_REQUEST['modify_same_user_level']));
    $goUser 											= $astDB->escape($_REQUEST['goUser']);
    $voicemail 											= $astDB->escape($_REQUEST['voicemail_id']);
    $vdc_agent_api_access 								= $astDB->escape($_REQUEST['vdc_agent_api_access']);
    $agent_choose_ingroups 								= $astDB->escape($_REQUEST['agent_choose_ingroups']);
    $vicidial_recording_override 						= $astDB->escape($_REQUEST['vicidial_recording_override']);
    $vicidial_transfers 								= $astDB->escape($_REQUEST['vicidial_transfers']);
    $closer_default_blended 							= $astDB->escape($_REQUEST['closer_default_blended']);
    $agentcall_manual 									= $astDB->escape($_REQUEST['agentcall_manual']);
    $scheduled_callbacks 								= $astDB->escape($_REQUEST['scheduled_callbacks']);
    $agentonly_callbacks 								= $astDB->escape($_REQUEST['agentonly_callbacks']);
    $agent_lead_search_override 						= $astDB->escape($_REQUEST['agent_lead_search_override']);
    $avatar 											= $astDB->escape($_REQUEST['avatar']);
    $enable_webrtc 										= $astDB->escape($_REQUEST['enable_webrtc']);
    $location 											= $astDB->escape($_REQUEST['location_id']);
	
    // Default Values
    $defActive 											= array( "Y", "N" );	
    $defmodify_same_user_level 							= array( "Y", "N" );
    $goactive 											= 1;

    // Error Checking
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
	} elseif (empty($user) && empty($userid)){
		$err_msg 										= error_handle("40002");
		$apiresults 									= array(
			"code" 											=> "40002", 
			"result" 										=> $err_msg
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user) || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $userid)){
		$err_msg 										= error_handle("41006", "user_id or user");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
		$err_msg 										= error_handle("41006", "pass");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $full_name)){
		$err_msg 										= error_handle("41006", "full_name");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_login)){
		$err_msg 										= error_handle("41002", "phone_login");
		$apiresults 									= array(
			"code" 											=> "41002", 
			"result" 										=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_pass)){
		$err_msg 										= error_handle("41004", "phone_pass");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 										= error_handle("41006", "active");
		$apiresults										= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
	} elseif (!in_array($modify_same_user_level,$defmodify_same_user_level) && $modify_same_user_level != null) {
		$err_msg 										= error_handle("41006", "modify_same_user_level");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
	} elseif ($user_level < 1 && $user_level !=null || $user_level > 9 && $user_level != null) {
		$err_msg 										= error_handle("41002", "user_level");
		$apiresults 									= array(
			"code" 											=> "41002", 
			"result" 										=> $err_msg
		);
	} elseif ($VARSERVTYPE == "gofree" && $hotkeys_active != null) {
		$err_msg 										= error_handle("10004", "hotkeys_active. Hotkeys is disabled");
		$apiresults 									= array(
			"code" 											=> "10004", 
			"result" 										=> $err_msg
		);
	} elseif ($modify_same_user_level != null) {
		$err_msg 										= error_handle("10004", "modify_same_user_level. modify_same_user_level is disabled");
		$apiresults 									= array(
			"code" 											=> "10004", 
			"result" 										=> $err_msg
		);
	} elseif (!empty($location)) {
		$result_location 								= go_check_location($location, $user_group);
		
		if ($result_location < 1) {
			$err_msg 									= error_handle("41006", "location. User group does not exist in the location selected.");
			$apiresults 								= array(
				"code" 										=> "41006", 
				"result" 									=> $err_msg
			);
		}
		
		$updateUserGoArray 								= array_merge($updateUserGoArray, array("location_id" => $location));
		$insertUserGoArray 								= array_merge($insertUserGoArray, array("location_id" => $location));					
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

			if ($active == "N") { 
				$goactive 								= 0; 
			}
					
			$updateUserGoArray 							= array(
				"name" 										=> $user, 
				"fullname" 									=> $full_name, 
				"phone" 									=> $phone_login, 
				"email" 									=> $email, 
				"avatar" 									=> $avatar, 
				"user_group" 								=> $user_group, 
				"role" 										=> $user_level, 
				"status" 									=> $goactive,
                "enable_webrtc"                             => $enable_webrtc
			);
			
			$insertUserGoArray 							= array(
				"userid" 									=> $userid, 
				"name" 										=> $user, 
				"fullname" 									=> $full_name, 
				"phone" 									=> $phone_login, 
				"avatar" 									=> $avatar, 
				"user_group"								=> $user_group, 
				"role" 										=> $user_level, 
				"status" 									=> $goactive
			);
			
			$fresults 									= $astDB
				->where("user", $user)
				->where("user", DEFAULT_USERS, "NOT IN")
				->getOne("vicidial_users");
			
			if ($astDB->count > 0) {
				$dataUser 								= $fresults['user'];
				$data_phone_login 						= $fresults['phone_login'];
				$dataUserLevel 							= $fresults['user_level'];
				$dataFullname 							= $fresults['full_name'];
				$dataUserGroup 							= $fresults['user_group'];
				$dataActive 							= $fresults['active'];
				$dataEmail 								= $fresults['email'];
				$dataHotkeys 							= $fresults['hotkeys_active'];
				$data_modify_same_user_level 			= $fresults['modify_same_user_level'];
				$dataVoicemail 							= $fresults['voicemail_id'];
				$data_vdc_agent_api_access 				= $fresults['vdc_agent_api_access'];
				$data_agent_choose_ingroups 			= $fresults['agent_choose_ingroups'];
				$data_vicidial_recording_override 		= $fresults['vicidial_recording_override'];
				$data_vicidial_transfers 				= $fresults['vicidial_transfers'];
				$data_closer_default_blended 			= $fresults['closer_default_blended'];
				$data_agentcall_manual 					= $fresults['agentcall_manual'];
				$data_scheduled_callbacks 				= $fresults['scheduled_callbacks'];
				$data_agentonly_callbacks 				= $fresults['agentonly_callbacks'];
				$data_agent_lead_search_override 		= $fresults['agent_lead_search_override'];
				
				if (is_null ($phone_login))
					$phone_login 						= $data_phone_login;
				if (is_null ($user_level))
					$user_level 						= $dataUserLevel;
				if (is_null ($full_name))
					$full_name 							= $dataFullname;
				if (is_null ($user_group))
					$user_group 						= $dataUserGroup;
				if (is_null ($active))
					$active 							= $dataActive;
				if (is_null ($email))
					$email 								= $dataEmail;
				if (is_null ($hotkeys_active))
					$hotkeys_active 					= $dataHotkeys;
				if (is_null ($modify_same_user_level))
					$modify_same_user_level 			= $data_modify_same_user_level;
				if (is_null ($voicemail))
					$voicemail 							= $dataVoicemail;
				if (is_null ($vdc_agent_api_access))
					$vdc_agent_api_access 				= $data_vdc_agent_api_access;
				if (is_null ($agent_choose_ingroups))
					$agent_choose_ingroups 				= $data_agent_choose_ingroups;
				if (is_null ($vicidial_recording_override))
					$vicidial_recording_override 		= $data_vicidial_recording_override;
				if (is_null ($vicidial_transfers))
					$vicidial_transfers 				= $data_vicidial_transfers;
				if (is_null ($closer_default_blended))
					$closer_default_blended 			= $data_closer_default_blended;
				if (is_null ($agentcall_manual))
					$agentcall_manual 					= $data_agentcall_manual;
				if (is_null ($scheduled_callbacks))
					$scheduled_callbacks 				= $data_scheduled_callbacks;
				if (is_null ($agentonly_callbacks))
					$agentonly_callbacks 				= $data_agentonly_callbacks;
				if (is_null ($agent_lead_search_override))
					$agent_lead_search_override 		= $data_agent_lead_search_override;
					
				if ($modify_same_user_level == "Y") {
					$modify_same_user_level 			= 1;
				} else {
					$modify_same_user_level 			= 0;
				}
					
/*				if (checkIfTenant ($log_group, $goDB)){
					$astDB->where("user_group", $log_group);
				}
				
				//# Check User Group if valid
				if ($user_group != null) {	
					$astDB->where("user_group", $user_group);
					$astDB->getOne("vicidial_user_groups", "user_group"); 

					if ($astDB->count > 0) {	*/		
				$update_array 							= array(
					"full_name" 							=> $full_name, 
					"email" 								=> $email,					
					"user_group" 							=> $user_group, 
					"active" 								=> $active, 
					"user_level" 							=> $user_level,	
					"voicemail_id"							=> $voicemail,
					"hotkeys_active" 						=> $hotkeys_active,  
					"vdc_agent_api_access"					=> $vdc_agent_api_access, 
					"agent_choose_ingroups" 				=> $agent_choose_ingroups, 
					"vicidial_recording_override" 			=> $vicidial_recording_override, 
					"vicidial_transfers" 					=> $vicidial_transfers, 
					"closer_default_blended"				=> $closer_default_blended, 
					"agentcall_manual" 						=> $agentcall_manual, 
					"scheduled_callbacks" 					=> $scheduled_callbacks, 
					"agentonly_callbacks" 					=> $agentonly_callbacks, 
					"modify_same_user_level" 				=> $modify_same_user_level,  
					"agent_lead_search_override" 			=> $agent_lead_search_override
				);
							
				if ($pass != null) {
					$fetch_passhash 					= $astDB->getOne("system_settings", "pass_hash_enabled,pass_key,pass_cost");
					$pass_hash_enabled 					= $fetch_passhash["pass_hash_enabled"];
					$pass_key 							= $fetch_passhash["pass_key"];
					$pass_cost 							= $fetch_passhash["pass_cost"];
					$pass_hash 							= encrypt_passwd($pass, $pass_cost, $pass_key);

					if ($pass_hash_enabled > 0) {
						$phones_array 					= array(
							"conf_secret" 					=> "",
							"pass" 							=> ""
						);
						
						$update_array 					= array_merge($update_array, array(
							"pass_hash" 					=> $pass_hash, 
							"pass" 							=> ((int)$enable_webrtc == 0) ? $pass : "", 
							"phone_pass" 					=> ((int)$enable_webrtc == 0) ? $phone_pass : ""
							)
						);

						$goDB->where("setting", "GO_agent_wss_sip");
						$fetch_value 					= $goDB->getOne("settings", "value");
						
						$value 							= $fetch_value["value"];						
						$realm 							= (!is_null ($value) || $value !== '') ? $value : 'goautodial.com';						
						$ha1 							= md5 ("{$phone_login}:{$realm}:{$pass}");
						$ha1b 							= md5 ("{$phone_login}@{$realm}:{$realm}:{$pass}");

						$subscriber_array 				= array(
							"password" 						=> "", 
							"ha1" 							=> $ha1,
							"ha1b" 							=> $ha1b
						);
					} else {
						$phones_array 					= array(
							"conf_secret" 					=> $pass,
							"pass" 							=> $pass
						);
						
						$update_array 					= array_merge($update_array, array(
							"pass_hash" 					=> $pass_hash, 
							"pass" 							=> $pass, 
							"phone_pass" 					=> $phone_pass
							)
						);

						$subscriber_array 				= array(
							"password" 						=> $pass, 
							"ha1" 							=> "", 
							"ha1b" 							=> ""
						);
					}
					
					$astDB->where("extension", $phone_login);
					$astDB->update("phones", $phones_array);

					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $phone_login", $log_group, $astDB->getLastQuery());
					
					$kamDB->where("username", $phone_login);
                    if (!empty($realm))
                        $kamDB->where("domain", $realm);
					$kamDB->update("subscriber", $subscriber_array);

					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $phone_login", $log_group, $kamDB->getLastQuery());
				}
					
				if ($phone_login != null) {
					$update_array 						= array_merge($update_array, array(
						"phone_login" 						=> $phone_login
					));
                    
                    $phones_array 					= array(
                        "user_group"                    => $user_group
                    );
                    
					$astDB->where("extension", $phone_login);
					$astDB->update("phones", $phones_array);
                    
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $phone_login", $log_group, $astDB->getLastQuery());
                    
                    if ($protocol != "EXTERNAL") { 
                        $rebuild 							= rebuildconfQuery($astDB); 
                    }
				}

				$astDB->where("user", $user);
				$queryUpdateUser 						= $astDB->update('vicidial_users', $update_array);
				
				$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified User: $user", $log_group, $astDB->getLastQuery());
				
				$goDB->where("name", $user);
				$fetch_userIDGo 						= $goDB->getOne("users", "userid");
				
				if ($goDB->count > 0) {
					$goDB->where("name", $user);
					$goDB->update('users', $updateUserGoArray);
					
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified User: $user", $log_group, $goDB->getLastQuery());
				} else {
					$goDB->insert('users', $insertUserGoArray); // insert record in goautodial.users
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified User: $user", $log_group, $goDB->getLastQuery());
				}

				$justgovoip_array						= array(
					"web_password" 							=> $phone_pass
				);
				
				$goDB->where("carrier_id", $user_group);
				$goDB->update('justgovoip_sippy_info', $justgovoip_array);	
				
				$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $phone_login", $log_group, $goDB->getLastQuery());
					
				if ($queryUpdateUser) {
					$apiresults 						= array(
						"result" 							=> "success"
					);					
				} else {
					$err_msg 							= error_handle("10010");
					$apiresults 						= array(
						"code" 								=> "10010", 
						"result" 							=> $err_msg
					);					
				}
/*					} else {
						$err_msg 							= error_handle("41004", "user_group. Doesn't exist");
						$apiresults 						= array(
							"code" 								=> "41004", 
							"result" 							=> $err_msg
						);
					}			
				} else {
					$err_msg 								= error_handle("41004", "user_group. Doesn't exist");
					$apiresults 							= array(
						"code" 									=> "41004", 
						"result" 								=> $err_msg
					);
				}*/
			} else {
				$err_msg 								= error_handle("41004", "user or user_id Doesn't exist");
				$apiresults 							= array(
					"code" 									=> "41004", 
					"result" 								=> $err_msg
				);
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
