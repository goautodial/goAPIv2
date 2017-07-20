<?php
   //////////////////////////////////#
   //# Name: goAddUserGroup.php                   //#
   //# Description: API to add new User Group     //#
   //# Version: 0.9                               //#
   //# Copyright: GOAutoDial Ltd. (c) 2011-2015   //#
   //# Written by: Jeremiah Sebastian V. Samatra  //#
   //# License: AGPLv2                            //#
   //////////////////////////////////#
    
    include_once ("../goFunctions.php");
 
    // POST or GET Variables
        $user_group= $_REQUEST['user_group'];
        $group_name = $_REQUEST['group_name'];
        $goUser = $_REQUEST['goUser'];
	//$values = $_REQUEST['items'];
        $group_level = $_REQUEST['group_level'];
	$ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    // Error checking
	if($user_group == null || $user_group == "") {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001","result" => $err_msg);
		//$apiresults = array("result" => "Error: User Group ID field is required."); 
	} else {
        if(strlen($user_group) < 3 ) {
            $err_msg = error_handle("41006", "user_group");
			$apiresults = array("code" => "41006","result" => $err_msg);
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
            $err_msg = error_handle("41004", "user_group");
			$apiresults = array("code" => "41004","result" => $err_msg);
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || $group_name == null){
			$err_msg = error_handle("41004", "group_name");
			$apiresults = array("code" => "41004","result" => $err_msg);
			//$apiresults = array("result" => "Error: Special characters found in group_name and must not be null");
        } else {
			/*if($group_level < 1 || $group_level > 9) {
					$apiresults = array("result" => "Error: Group Level Value should be in between 1 and 9");
			} else {*/

			$groupId = go_get_groupid($goUser);

			if (!checkIfTenant($groupId)) {
				$ul = "WHERE user_group='$user_group'";
				$group_type = "Multi-tenant";
			} else {
				$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
				$group_type = "Default";
			}

			$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
			$rsltv = mysqli_query($link, $query);
			$countResult = mysqli_num_rows($rsltv);

			if($countResult > 0) {
				$err_msg = error_handle("41004", "user_group. Already exists");
				$apiresults = array("code" => "41004","result" => $err_msg);
				//$apiresults = array("result" => "Error: User Group already exist.");
			} else {
				
				$query = "INSERT INTO vicidial_user_groups (user_group, group_name, allowed_campaigns) VALUES ('$user_group', '$group_name', ' -');";
				$rsltv = mysqli_query($link, $query);
				
				$queryCheck = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
				$rsltvCheck = mysqli_query($link, $queryCheck);
				$countCheck = mysqli_num_rows($rsltvCheck);
	/* MANUAL USER GROUP
	
				$queryUG = "UPDATE vicidial_user_groups SET group_name='$group_name',  forced_timeclock_login='$forced_timeclock_login',  shift_enforcement='$shift_enforcement',  agent_status_view_time='$agent_status_view_time',  agent_call_log_view='$agent_call_log_view',  agent_xfer_consultative='$agent_xfer_consultative',  agent_xfer_dial_override='$agent_xfer_dial_override',  agent_xfer_vm_transfer='$agent_xfer_vm_transfer',  agent_xfer_blind_transfer='$agent_xfer_blind_transfer',  agent_xfer_dial_with_customer='$agent_xfer_dial_with_customer',  agent_xfer_park_customer_dial='$agent_xfer_park_customer_dial',  agent_fullscreen='$agent_fullscreen',  allowed_campaigns='$allowed_campaigns',  agent_status_viewable_groups='$agent_status_viewable_groups',  allowed_reports='$allowed_reports',  admin_viewable_groups='$admin_viewable_groups',  admin_viewable_call_times='$admin_viewable_call_times' WHERE user_group='$user_group';";
	
	*/
				$default_permission = '{"dashboard":{"dashboard_display":"Y"},"user":{"user_create":"C","user_read":"R","user_update":"N","user_delete":"N"},"campaign":{"campaign_create":"N","campaign_read":"R","campaign_update":"U","campaign_delete":"N"},"disposition":{"disposition_create":"C","disposition_update":"U","disposition_delete":"N"},"pausecodes":{"pausecodes_create":"C","pausecodes_read":"R","pausecodes_update":"U","pausecodes_delete":"N"},"hotkeys":{"hotkeys_create":"C","hotkeys_read":"R","hotkeys_delete":"N"},"list":{"list_create":"C","list_read":"R","list_update":"U","list_delete":"N","list_upload":"C"},"customfields":{"customfields_create":"C","customfields_read":"R","customfields_update":"U","customfields_delete":"N"},"script":{"script_create":"C","script_read":"R","script_update":"U","script_delete":"N"},"inbound":{"inbound_create":"C","inbound_read":"R","inbound_update":"U","inbound_delete":"N"},"ivr":{"ivr_create":"C","ivr_read":"R","ivr_update":"U","ivr_delete":"N"},"did":{"did_create":"C","did_read":"R","did_update":"U","did_delete":"N"},"voicefiles":{"voicefiles_upload":"C","voicefiles_play":"Y","voicefiles_download":"Y"},"moh":{"moh_create":"C","moh_read":"R","moh_update":"U","moh_delete":"N"},"reportsanalytics":{"reportsanalytics_statistical_display":"Y","reportsanalytics_agent_time_display":"Y","reportsanalytics_agent_performance_display":"Y","reportsanalytics_dial_status_display":"Y","reportsanalytics_agent_sales_display":"Y","reportsanalytics_sales_tracker_display":"Y","reportsanalytics_inbound_call_display":"Y","reportsanalytics_export_call_display":"Y"},"recordings":{"recordings_display":"Y"},"support":{"support_display":"Y"},"multi-tenant":{"tenant_create":"N","tenant_display":"N","tenant_update":"N","tenant_delete":"N","tenant_logs":"N","tenant_calltimes":"N","tenant_phones":"N","tenant_voicemails":"N"},"chat":{"chat_create":"C","chat_read":"R","chat_update":"U","chat_delete":"D"},"osticket":{"osticket_create":"C","osticket_read":"R","osticket_update":"U","osticket_delete":"D"}}';
				$queryGL = "INSERT INTO user_access_group (user_group,group_level,permissions) VALUES ('$user_group','$group_level','$default_permission');";
				$rsltvGL = mysqli_query($linkgo, $queryGL);
				
				$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New User Group: $user_group", $log_group, $query);
				
				if($countCheck > 0) {
					$apiresults = array("result" => "success", "user_group" => $user_group);
				} else {
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010","result" => $err_msg);
				}
			}
		}
		}
}	}
?>
