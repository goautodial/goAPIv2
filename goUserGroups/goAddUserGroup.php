<?php
/**
 * @file        goAddUserGroup.php
 * @brief       API to add new User Group 
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
	$user_group = $astDB->escape($_REQUEST['user_group']);
	$group_name = $astDB->escape($_REQUEST['group_name']);
	$goUser = $astDB->escape($_REQUEST['goUser']);
	$group_level = $astDB->escape($_REQUEST['group_level']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);

	$log_user = $session_user;
	$log_group = go_get_groupid($session_user);

    // Error checking
	if(!isset($session_user) || is_null()){
		$apiresults = array("result" => "Error: Session User Not Defined.");
	}elseif(is_null($user_group) || $user_group == "") {
		$apiresults = array("result" => "Error: User Group ID field is required."); 
	} elseif(strlen($user_group) < 3 ) {
        $err_msg = error_handle("41006", "user_group");
		$apiresults = array("code" => "41006","result" => $err_msg);
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
        $err_msg = error_handle("41004", "user_group");
		$apiresults = array("code" => "41004","result" => $err_msg);
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || $group_name == null){
		$err_msg = error_handle("41004", "group_name");
		$apiresults = array("code" => "41004","result" => $err_msg);
	} else {

		if (!checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $user_group);
			//$ul = "WHERE user_group='$user_group'";
			$group_type = "Multi-tenant";
		} else {
			$astDB->where("user_group", $user_group);
			$astDB->where("user_group", $log_group);
			//$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
			$group_type = "Default";
		}

		$astDB->getOne("vicidial_user_groups", "user_group");
		//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		
		$countResult = $astDB->count;

		if($countResult > 0) {
			$err_msg = error_handle("41004", "user_group. Already exists");
			$apiresults = array("code" => "41004","result" => $err_msg);
			//$apiresults = array("result" => "Error: User Group already exist.");
		} else {
			
			$data = Array("user_group" => $user_group, "group_name" => $group_name, "allowed_campaigns" => " -");
			$mainQuery = $astDB->insert("vicidial_user_groups", $data);
			//$query = "INSERT INTO vicidial_user_groups (user_group, group_name, allowed_campaigns) VALUES ('$user_group', '$group_name', ' -');";
			
			$default_permission = '{"dashboard":{"dashboard_display":"Y"},"user":{"user_create":"C","user_read":"R","user_update":"N","user_delete":"N"},"campaign":{"campaign_create":"N","campaign_read":"R","campaign_update":"U","campaign_delete":"N"},"disposition":{"disposition_create":"C","disposition_update":"U","disposition_delete":"N"},"pausecodes":{"pausecodes_create":"C","pausecodes_read":"R","pausecodes_update":"U","pausecodes_delete":"N"},"hotkeys":{"hotkeys_create":"C","hotkeys_read":"R","hotkeys_delete":"N"},"list":{"list_create":"C","list_read":"R","list_update":"U","list_delete":"N","list_upload":"C"},"customfields":{"customfields_create":"C","customfields_read":"R","customfields_update":"U","customfields_delete":"N"},"script":{"script_create":"C","script_read":"R","script_update":"U","script_delete":"N"},"inbound":{"inbound_create":"C","inbound_read":"R","inbound_update":"U","inbound_delete":"N"},"ivr":{"ivr_create":"C","ivr_read":"R","ivr_update":"U","ivr_delete":"N"},"did":{"did_create":"C","did_read":"R","did_update":"U","did_delete":"N"},"voicefiles":{"voicefiles_upload":"C","voicefiles_play":"Y","voicefiles_download":"Y"},"moh":{"moh_create":"C","moh_read":"R","moh_update":"U","moh_delete":"N"},"reportsanalytics":{"reportsanalytics_statistical_display":"Y","reportsanalytics_agent_time_display":"Y","reportsanalytics_agent_performance_display":"Y","reportsanalytics_dial_status_display":"Y","reportsanalytics_agent_sales_display":"Y","reportsanalytics_sales_tracker_display":"Y","reportsanalytics_inbound_call_display":"Y","reportsanalytics_export_call_display":"Y"},"recordings":{"recordings_display":"Y"},"support":{"support_display":"Y"},"multi-tenant":{"tenant_create":"N","tenant_display":"N","tenant_update":"N","tenant_delete":"N","tenant_logs":"N","tenant_calltimes":"N","tenant_phones":"N","tenant_voicemails":"N"},"chat":{"chat_create":"C","chat_read":"R","chat_update":"U","chat_delete":"D"},"osticket":{"osticket_create":"C","osticket_read":"R","osticket_update":"U","osticket_delete":"D"}}';
			
			$subData = Array("user_group" => $user_group, "group_level" => $group_level, "permissions" => $default_permission);
			$subQuery = $goDB->insert("user_access_group", $subData);
			//$queryGL = "INSERT INTO user_access_group (user_group,group_level,permissions) VALUES ('$user_group','$group_level','$default_permission');";
				
			$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added New User Group: $user_group", $log_group, $query);
				
			if($mainQuery) {
				$apiresults = array("result" => "success", "user_group" => $user_group);
			} else {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010","result" => $err_msg);
			}
		}
	}
?>
