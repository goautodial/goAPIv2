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
    $user_group 										= $astDB->escape($_REQUEST['user_group']);
    $group_name 										= $astDB->escape($_REQUEST['group_name']);
    $group_level 										= $astDB->escape($_REQUEST['group_level']);
    $allowed_campaigns 									= $_REQUEST['allowed_campaigns'];
    $allowed_usergroups 								= $_REQUEST['allowed_usergroups'];
	$permissions										= $_REQUEST['permissions'];
    $forced_timeclock_login 							= strtoupper($astDB->escape($_REQUEST['forced_timeclock_login']));
    $shift_enforcement 									= strtoupper($astDB->escape($_REQUEST['shift_enforcement']));

    // Defaul Values
    $defFTL 											= array( "Y", "N", "ADMIN_EXEMPT" );	
    $defSE 												= array( "OFF", "START", "ALL" );

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
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
        $apiresults 									= array(
			"result" 										=> "Error: Special characters found in user_group"
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
        $apiresults 									= array(
			"result" 										=> "Error: Special characters found in group_name"
		);
    } elseif ($group_level < 1 && $group_level != null || $group_level > 9 && $group_level != null) {
        $apiresults 									= array(
			"result" 										=> "Error: Group Level Value should be in between 1 and 9"
		);
    } elseif (!in_array($forced_timeclock_login,$defFTL) && $forced_timeclock_login != null) {
        $apiresults 									= array(
			"result" 										=> "Error: Default value for forced_timeclock_login is Y, N or ADMIN_EXEMPT only."
		);
    } elseif (!in_array($shift_enforcement,$defSE) && $shift_enforcement != null) {
        $apiresults	 									= array(
			"result" 										=> "Error: Default value for shift_enforcement is OFF, START or ALL only."
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
				if (strtoupper($log_group) != "ADMIN") {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}	
			}	

			$astDB->where("user_group", $user_group);
			$query 										= $astDB->getOne("vicidial_user_groups");
        
			if ($astDB->count > 0) {		
				//user_group, group_name, group_level, forced_timeclock_login, shift_enforcement
				if (is_null($allowed_usergroups) || empty($allowed_usergroups)) {
					$allowed_usergroups 				= $query["user_group"];
				}
				
				if (is_null($group_name)) {
					$group_name 						= $query["group_name"]; 
				}
				
				if (is_null($forced_timeclock_login)) {
					$forced_timeclock_login 			= $query["forced_timeclock_login"];
				}
				
				if (is_null($shift_enforcement)) {
					$shift_enforcement 					= $query["shift_enforcement"];
				}
				
				if (is_null($allowed_campaigns)) {
					$allowed_campaigns 					= $query["allowed_campaigns"];
				}           

				$data 									= array(
					"group_name" 							=> $group_name,
					"forced_timeclock_login" 				=> $forced_timeclock_login,
					"shift_enforcement" 					=> $shift_enforcement,
					"allowed_campaigns" 					=> $allowed_campaigns,
					"admin_viewable_groups" 				=> $allowed_usergroups
				);

				$astDB->where("user_group", $user_group);
				$q_update								= $astDB->update("vicidial_user_groups", $data);
				$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified User Group: $user_group", $log_group, $astDB->getLastQuery());
				
				$goDB->where("user_group", $user_group);
				$querygo 								= $goDB->getOne("user_access_group");           
				
				if ($goDB->count > 0) {
					if (is_null($group_level)) {
						$group_level 					= $querygo["group_level"];
					}
					
					if (is_null($permissions) || $user_group == "ADMIN" || $user_group == "AGENTS") {
						$permissions 					= $querygo["permissions"];
					} 

					$datago 							= array(
						"group_level" 						=> $group_level,
						"permissions" 						=> $permissions
					);

					$goDB->where("user_group", $user_group);
					$qgo_update							= $goDB->update("user_access_group", $datago); 
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified User Group: $user_group", $log_group, $goDB->getLastQuery());
				}
				
				if (!$q_update) {
					$apiresults 						= array(
						"result" 							=> "Error: Failed Update, Check your details"
					);
				} else {
					$apiresults 						= array(
						"result" 							=> "success", 
						"data" 								=> $q_update
					);
				}
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: User Group doesn't exist"
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
