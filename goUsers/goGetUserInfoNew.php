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
        
        if (!checkIfTenant($groupId, $astDB)) { $astDB->where("user_id", $user_id); } 
    	else { $astDB->where("user_id", $user_id); $astDB->where("user_group", $groupId); }
        
        $fresults = $astDB->getOne("vicidial_users", "user_id,user,full_name,email,user_group,active,user_level,phone_login,phone_pass,voicemail_id,hotkeys_active,vdc_agent_api_access,agent_choose_ingroups,vicidial_recording_override,vicidial_transfers,closer_default_blended,agentcall_manual,scheduled_callbacks,agentonly_callbacks,agent_lead_search_override");
        $fresultsgo = $goDB->getOne("users", "userid,avatar,gcal,calendar_apikey,calendar_id");
                
        $data = array_merge($fresults, $fresultsgo);        
        $log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of User $user", $log_group);
        
		if(!empty($data)) { $apiresults = array("result" => "success", "data" => $data); } 
		else { $apiresults = array("result" => "Error: User Group doesn't exist."); }                            
	}
  

?>
