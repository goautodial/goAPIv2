<?php
/**
 * @file        goGetUserInfoNew.php
 * @brief       API to get specific user details 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho <demianb@goautodial.com>
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
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
    $user_id = $astDB->escape($_REQUEST['user_id']);
    $ip_address = $astDB->escape($_REQUEST['log_ip']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
        
    // Check user_id if its null or empty
    if($user_id == null) {
		$err_msg = error_handle("40001");
        $apiresults = array("code" => "40001","result" => $err_msg);
    } else {
        $log_user = $session_user;
        $groupId = go_get_groupid($session_user, $astDB);
        
        if (!checkIfTenant($groupId, $astDB)) {
			$astDB->where("user_id", $user_id);
    	} else {
			$astDB->where("user_id", $user_id);
            $astDB->where("user_group", $groupId); 
		}
        
        $fresults = $astDB->getOne("vicidial_users", "user_id,user,full_name,email,user_group,active,user_level,phone_login,phone_pass,voicemail_id,hotkeys_active,vdc_agent_api_access,agent_choose_ingroups,vicidial_recording_override,vicidial_transfers,closer_default_blended,agentcall_manual,scheduled_callbacks,agentonly_callbacks,agent_lead_search_override");
        $fresultsgo = $goDB->getOne("users", "userid,avatar,gcal,calendar_apikey,calendar_id");
        
        $countResult = $astDB->count;
        
		if($countResult > 0) {						
            $dataUserId = $fresults['user_id'];
            $dataUser = $fresults['user'];
            $dataFullname = $fresults['full_name'];
            $dataEmail = $fresults['email'];
            $dataUserGroup = $fresults['user_group'];
            $dataUserLevel = $fresults['user_level'];
            $dataActive = $fresults['active'];
            $dataPhoneLogin = $fresults['phone_login'];
            $dataPhonePass = $fresults['phone_pass'];
            $dataVoicemailId = $fresults['voicemail_id'];
            $dataHotkeysActive = $fresults['hotkeys_active'];
            $dataVdcAgentApiAccess = $fresults['vdc_agent_api_access'];
            $dataAgentChooseIngroup = $fresults['agent_choose_ingroups'];
            $dataVicidialRecordingOverride = $fresults['vicidial_recording_override'];
            $dataVicidialTransfers = $fresults['vicidial_transfers'];
            $dataCloserDefaultBlended = $fresults['closer_default_blended'];
            $dataAgentCallManual = $fresults['agentcall_manual'];
            $dataScheduleCallbacks = $fresults['scheduled_callbacks'];
            $dataAgentOnlyCallbacks = $fresults['agentonly_callbacks'];
            $dataAgentLeadsearchOverride = $fresults['agent_lead_search_override'];
         		
			$datagoUserid = $fresultsgo['userid'];
			$datagoAvatar = $fresultsgo['avatar'];
			$datagoGcal = $fresultsgo['gcal'];
			$datagoCalendarApiKey = $fresultsgo['calendar_apikey'];
			$datagoCalendarId = $fresultsgo['calendar_id'];
			
            $apiresults = array("result" => "success", "user_id" => $dataUserId, "user" => $dataUser, "full_name" => $dataFullname, "email" => $dataEmail, "user_group" => $dataUserGroup, "user_level" => $dataUserLevel, "active" => $dataActive, "phone_login" => $dataPhoneLogin, "phone_pass" => $dataPhonePass, "voicemail_id" => $dataVoicemailId, "hotkeys_active" => $dataHotkeysActive, "vdc_agent_api_access" => $dataVdcAgentApiAccess, "agent_choose_ingroups" => $dataAgentChooseIngroup, "vicidial_recording_override" => $dataVicidialRecordingOverride, "vicidial_transfers" => $dataVicidialTransfers, "closer_default_blended" => $dataCloserDefaultBlended, "agentcall_manual" => $dataAgentCallManual, "scheduled_callbacks" => $dataScheduleCallbacks, "agentonly_callbacks" => $dataAgentOnlyCallbacks, "agent_lead_search_override" => $dataAgentLeadsearchOverride, "userid" => $datagoUserid, "avatar" => $datagoAvatar, "gcal" => $datagoGcal, "calendar_apikey" => $datagoCalendarApiKey, "calendar_id" => $datagoCalendarId);
			
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of User $user", $log_group);
		} else {
			$apiresults = array("result" => "Error: User doesn't exist.");
		}        
                            
	}
  

?>
