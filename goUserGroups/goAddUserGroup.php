<?php
/**
 * @file        goAddUserGroup.php
 * @brief       API to add new User Group 
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
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
*/
    
    include_once ("goAPI.php");
 	
    // POST or GET Variables
	$user_group 										= $astDB->escape($_REQUEST['user_group']);
	$group_name 										= $astDB->escape($_REQUEST['group_name']);
	$group_level 										= $astDB->escape($_REQUEST['group_level']);	

	// Error Checking
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
	} elseif (empty($user_group) || is_null($user_group)) {
		$apiresults 									= array(
			"result" 										=> "Error: User Group ID Not Defined."
		); 
	} elseif(strlen($user_group) < 3 ) {
        $err_msg 										= error_handle("41006", "user_group");
		$apiresults										= array(
			"code" 											=> "41006",
			"result" 										=> $err_msg
		);
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
        $err_msg 										= error_handle("41004", "user_group");
		$apiresults 									= array(
			"code" 											=> "41004",
			"result" 										=> $err_msg
		);
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || $group_name == null){
		$err_msg 										= error_handle("41004", "group_name");
		$apiresults 									= array(
			"code" 											=> "41004",
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
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}				
			}
			
			$astDB->where("user_group", $user_group);
			$astDB->getOne("vicidial_user_groups", "user_group");		

			if($astDB->count > 0) {
				$err_msg 								= error_handle("41004", "user_group. Already exists");
				$apiresults 							= array(
					"code" 									=> "41004",
					"result" 								=> $err_msg
				);
				//$apiresults = array("result" => "Error: User Group already exist.");
			} else {			
				$data 									= array(
					"user_group"		 					=> $user_group, 
					"group_name" 							=> $group_name, 
					"allowed_campaigns" 					=> " -"
				);

				$query 									= $astDB->insert("vicidial_user_groups", $data);
				$log_id 								= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User Group: $user_group", $log_group, $astDB->getLastQuery());
				
				$default_permission 					= '{"dashboard":{"dashboard_display":"Y"}, "user":{"user_create":"C","user_read":"R","user_update":"N","user_delete":"N"}, "campaign":{"campaign_create":"N","campaign_read":"R","campaign_update":"U","campaign_delete":"N"}, "disposition":{"disposition_create":"C","disposition_update":"U","disposition_delete":"N"}, "pausecodes":{"pausecodes_create":"C","pausecodes_read":"R","pausecodes_update":"U","pausecodes_delete":"N"}, "hotkeys":{"hotkeys_create":"C","hotkeys_read":"R","hotkeys_delete":"N"}, "list":{"list_create":"C","list_read":"R","list_update":"U","list_delete":"N","list_upload":"C"}, "customfields":{"customfields_create":"C","customfields_read":"R","customfields_update":"U","customfields_delete":"N"}, "script":{"script_create":"C","script_read":"R","script_update":"U","script_delete":"N"}, "inbound":{"inbound_create":"C","inbound_read":"R","inbound_update":"U","inbound_delete":"N"}, "ivr":{"ivr_create":"C","ivr_read":"R","ivr_update":"U","ivr_delete":"N"}, "did":{"did_create":"C","did_read":"R","did_update":"U","did_delete":"N"}, "voicefiles":{"voicefiles_upload":"C","voicefiles_play":"Y","voicefiles_download":"Y"}, "moh":{"moh_create":"C","moh_read":"R","moh_update":"U","moh_delete":"N"}, "reportsanalytics":{"reportsanalytics_statistical_display":"Y","reportsanalytics_agent_time_display":"Y","reportsanalytics_agent_performance_display":"Y","reportsanalytics_dial_status_display":"Y","reportsanalytics_agent_sales_display":"Y","reportsanalytics_sales_tracker_display":"Y","reportsanalytics_inbound_call_display":"Y","reportsanalytics_export_call_display":"Y"}, "recordings":{"recordings_display":"Y"},"support":{"support_display":"Y"}, "multi-tenant":{"tenant_create":"N","tenant_display":"N","tenant_update":"N","tenant_delete":"N","tenant_logs":"N","tenant_calltimes":"N","tenant_phones":"N","tenant_voicemails":"N"}, "chat":{"chat_create":"C","chat_read":"R","chat_update":"U","chat_delete":"D"}, "osticket":{"osticket_create":"C","osticket_read":"R","osticket_update":"U","osticket_delete":"D"}}';
				
				$subData 								= array(
					"user_group" 							=> $user_group,
					"group_level" 							=> $group_level, 
					"permissions" 							=> $default_permission
				);
				
				$goDB->where("user_group", $user_group);
				$goDB->getOne("user_access_group", "user_group");
				
				if ($goDB->count < 1) {
					$querygo 							= $goDB->insert("user_access_group", $subData);				
					$log_id								= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User Group: $user_group", $log_group, $goDB->getLastQuery());				
				}
				
				
				if ($query) {
					$apiresults 						= array(
						"result" 							=> "success", 
						"data" 								=> array($astDB->getLastQuery(), $goDB->getLastQuery())
					);
				} else {
					$err_msg 							= error_handle("10010");
					$apiresults 						= array(
						"code" 								=> "10010",
						"result" 							=> $err_msg
					);
				}
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
