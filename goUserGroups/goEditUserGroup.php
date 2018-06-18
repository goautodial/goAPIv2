<?php
/**
 * @file        goEditUserGroup.php
 * @brief       API to edit specific User Group
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Original Author <alex@goautodial.com>
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

    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $user_group = $astDB->escape($_REQUEST['user_group']);
    $group_name = $astDB->escape($_REQUEST['group_name']);
    $group_level = $astDB->escape($_REQUEST['group_level']);
    $allowed_campaigns = $astDB->escape($_REQUEST['allowed_campaigns']);
    $allowed_usergroups = $astDB->escape($_REQUEST['allowed_usergroups']);
    $permissions = $astDB->escape($_REQUEST['permissions']);
    $forced_timeclock_login = strtoupper($astDB->escape($_REQUEST['forced_timeclock_login']));
    $shift_enforcement = strtoupper($astDB->escape($_REQUEST['shift_enforcement']));

	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);

    // Defaul Values
    $defFTL = array('Y','N','ADMIN_EXEMPT');
    $defSE = array('OFF','START','ALL');

    if(!isset($session_user) || is_null($session_user)){
        $apiresults = array("result" => "Error: Missing Required Parameters.");
    }elseif(is_null($user_group)) {
        $apiresults = array("result" => "Error: Set a value for User Group.");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
        $apiresults = array("result" => "Error: Special characters found in user_group");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
        $apiresults = array("result" => "Error: Special characters found in group_name");
    } elseif($group_level < 1 && $group_level != null || $group_level > 9 && $group_level != null) {
        $apiresults = array("result" => "Error: Group Level Value should be in between 1 and 9");
    } elseif(!in_array($forced_timeclock_login,$defFTL) && $forced_timeclock_login != null) {
        $apiresults = array("result" => "Error: Default value for aforced_timeclock_login is Y, N or ADMIN_EXEMPT only.");
    } elseif(!in_array($shift_enforcement,$defSE) && $shift_enforcement != null) {
        $apiresults = array("result" => "Error: Default value for shift_enforcement is OFF, START or ALL only.");
    } else {
    
        $groupId = $log_group;
        if (!checkIfTenant($groupId, $goDB)) {
            $astDB->where("user_group", $user_group);
            //$ul = "WHERE user_group='$user_group'";
        } else {
            $astDB->where("user_group", $user_group);
            $astDB->where("user_group", $groupId);
            //$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
        }

        $cols = array("user_group", "group_name", "forced_timeclock_login", "shift_enforcement", "allowed_campaigns", "admin_viewable_groups");
        $fresults = $astDB->getOne("vicidial_user_groups", NULL, $cols);
        //$query = "SELECT user_group, group_name, forced_timeclock_login, shift_enforcement FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
        $countResult = $astDB->count;
		if($countResult > 0) {
			 //user_group, group_name, group_level, forced_timeclock_login, shift_enforcement
			if(is_null($group_name)){$group_name = $fresults["group_name"];} 
            if(is_null($forced_timeclock_login)){$forced_timeclock_login = $fresults["forced_timeclock_login"];} 
            if(is_null($shift_enforcement)){$shift_enforcement = $fresults["shift_enforcement"];}
            if(is_null($allowed_campaigns)){$allowed_campaigns = $fresults["allowed_campaigns"];}
            if(is_null($allowed_usergroups)){$allowed_usergroups = $fresults["allowed_usergroups"];}

            $data = Array(
                        "group_name" => $group_name,
                        "forced_timeclock_login" => $forced_timeclock_login,
                        "shift_enforcement" => $shift_enforcement,
                        "allowed_campaigns" => $allowed_campaigns,
                        "admin_viewable_groups" => $allowed_usergroups
                    );

            $astDB->where("user_group", $user_group);
            $astUpdate = $astDB->update("vicidial_user_groups", $data);
			//$query = "UPDATE vicidial_user_groups SET group_name='$group_name', forced_timeclock_login='$forced_timeclock_login', shift_enforcement='$shift_enforcement', allowed_campaigns='$allowed_campaigns', admin_viewable_groups='$allowed_usergroups' WHERE user_group='$user_group';";

			$goDB->where("user_group", $user_group);
            $fresultsgo = $goDB->getOne("user_access_group", "group_level, permissions");
            if(is_null($group_level)){$group_level = $fresultsgo["group_level"];} 
            if(is_null($permissions)){$permissions = $fresultsgo["permissions"];} 

            $goData = Array(
                        "group_level" => $group_level,
                        "permissions" => $permissions
                    );

            $goDB->where("user_group", $user_group);
			$goUpdate = $goDB->update("user_access_group", $goData);
            //$queryGL = "UPDATE user_access_group SET group_level = '$group_level', permissions = '$permissions' WHERE user_group='$user_group';";
            
			if(!$astUpdate){
				$apiresults = array("result" => "Error: Failed Update, Check your details");
			} else {
				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified User Group: $group", $log_group, $astUpdate);
				$apiresults = array("result" => "success", "query" => $astUpdate);
			}
		} else {
			$apiresults = array("result" => "Error: User Group doesn't exist".$astUpdate);
		}
	}
?>
