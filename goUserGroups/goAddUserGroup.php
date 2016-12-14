<?php
   ####################################################
   #### Name: goAddUserGroup.php                   ####
   #### Description: API to add new User Group     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $user_group= $_REQUEST['user_group'];
        $group_name = $_REQUEST['group_name'];
        $goUser = $_REQUEST['goUser'];
	//$values = $_REQUEST['items'];
        $group_level = $_REQUEST['group_level'];
	$ip_address = $_REQUEST['hostname'];

    ### Error checking
	if($user_group == null || $user_group == "") { 
		$apiresults = array("result" => "Error: User Group ID field is required."); 
	} else {
        if(strlen($user_group) < 3 ) {
                $apiresults = array("result" => "Error: User Group is Minimun of 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
                $apiresults = array("result" => "Error: Special characters found in user_group");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || $group_name == null){
                $apiresults = array("result" => "Error: Special characters found in group_name and must not be null");
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
			$apiresults = array("result" => "Error: User Group already exist.");
		} else {
/*
                                $postItem = rtrim($values,"&");
                                $items = explode("&",str_replace(";","",$postItem));
                                foreach ($items as $item)
                                {
                                        list($var,$val) = split("=",$item);
                                        if (strlen($val) > 0)
                                        {
						if($var=="group_level"){
							$group_level = "$val";
						} else {
                                                $varSQL .= "$var,";
                                                $valSQL .= "'".str_replace('+',' ',mysql_real_escape_string($val))."',";

                                                if ($var=="user_group"){
                                                        $group="$val";
                                                        $user_group['user_group'] = $val;
                                                }
						}
                                        }
                                }

                                $varSQL = rtrim($varSQL,",");
                                $valSQL = rtrim($valSQL,",");
                                $itemSQL = "($varSQL) VALUES ($valSQL)";
                                */
                                $query = "INSERT INTO vicidial_user_groups (user_group, group_name) VALUES ('$user_group', '$group_name');";
				$rsltv = mysqli_query($link, $query);

			                $queryCheck = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                			$rsltvCheck = mysqli_query($link, $queryCheck);
                			$countCheck = mysqli_num_rows($rsltvCheck);
/* MANUAL USER GROUP


			$queryUG = "UPDATE vicidial_user_groups SET group_name='$group_name',  forced_timeclock_login='$forced_timeclock_login',  shift_enforcement='$shift_enforcement',  agent_status_view_time='$agent_status_view_time',  agent_call_log_view='$agent_call_log_view',  agent_xfer_consultative='$agent_xfer_consultative',  agent_xfer_dial_override='$agent_xfer_dial_override',  agent_xfer_vm_transfer='$agent_xfer_vm_transfer',  agent_xfer_blind_transfer='$agent_xfer_blind_transfer',  agent_xfer_dial_with_customer='$agent_xfer_dial_with_customer',  agent_xfer_park_customer_dial='$agent_xfer_park_customer_dial',  agent_fullscreen='$agent_fullscreen',  allowed_campaigns='$allowed_campaigns',  agent_status_viewable_groups='$agent_status_viewable_groups',  allowed_reports='$allowed_reports',  admin_viewable_groups='$admin_viewable_groups',  admin_viewable_call_times='$admin_viewable_call_times' WHERE user_group='$user_group';";

*/
								$default_permission = '{"dashboard":{"dashboard_display":"Y"},"user":{"user_create":"C","user_read":"R","user_update":"N","user_delete":"N"},"campaign":{"campaign_create":"N","campaign_read":"R","campaign_update":"U","campaign_delete":"N"},"disposition":{"disposition_create":"C","disposition_update":"U","disposition_delete":"N"},"pausecodes":{"pausecodes_create":"C","pausecodes_read":"R","pausecodes_update":"U","pausecodes_delete":"N"},"hotkeys":{"hotkeys_create":"C","hotkeys_read":"R","hotkeys_delete":"N"},"list":{"list_create":"C","list_read":"R","list_update":"U","list_delete":"N","list_upload":"C"},"customfields":{"customfields_create":"C","customfields_read":"R","customfields_update":"U","customfields_delete":"N"},"script":{"script_create":"C","script_read":"R","script_update":"U","script_delete":"N"},"inbound":{"inbound_create":"C","inbound_read":"R","inbound_update":"U","inbound_delete":"N"},"voicefiles":{"voicefiles_upload":"C","voicefiles_delete":"N"},"reportsanalytics":{"reportsanalytics_display":"Y"},"recordings":{"recordings_display":"Y"},"support":{"support_display":"Y"},"multi-tenant":{"tenant_create":"N","tenant_display":"N","tenant_update":"N","tenant_delete":"N","tenant_logs":"N","tenant_calltimes":"N","tenant_phones":"N","tenant_voicemails":"N"}}';
                                $queryGL = "INSERT INTO user_access_group (user_group,group_level,permissions) VALUES ('$user_group','$group_level','$default_permission');";
                                $rsltvGL = mysqli_query($linkgo, $queryGL);


					$SQLdate = date("Y-m-d H:i:s");
					$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New User Group $user_group','INSERT INTO vicidial_user_groups (user_group,group_name) VALUES ($user_group,$group_name)');";
					$rsltvLog = mysqli_query($linkgo, $queryLog);
					
                				if($countCheck > 0) {
							$apiresults = array("result" => "success");
						} else {
							$apiresults = array("result" => "Error: Please check your details.");
						}
                                /*if ($this->db->affected_rows())
                                {
                                        $this->commonhelper->auditadmin("ADD","Added New User Group $group","INSERT INTO vicidial_user_groups $itemSQL;");

                                        if($this->go_access->go_check_access_exist($user_group['user_group'])){
                                             $groupings = array_merge($user_group,array('permissions'=>$_POST['permiso'],'group_level'=>$group_level));
                                             $this->go_access->goautodialDB->insert('user_access_group',$groupings);
                                             $this->commonhelper->auditadmin("ADD","Add new Group Access: $group");
                                        }

                                        $return = "SUCCESS";
                                }*/

			}
		}	}
		//}
}	}
?>
