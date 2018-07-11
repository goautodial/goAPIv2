<?php
/**
 * @file        goGetUserGroupsList.php
 * @brief       API to get all user group details
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
 * @author      Alexander Jim H. Abenoja
 * @author      Jeremiah Sebastian V. Samatra
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
	
	$log_user 							= $session_user;
	$log_group 							= go_get_groupid($session_user, $astDB); 
	$ip_address 						= $astDB->escape($_REQUEST["log_ip"]);
    
    if (!isset($session_user) || is_null($session_user)) {
    	$apiresults 					= array(
			"result" 						=> "Error: Missing Required Parameters."
		); 
	} else {
		if (!checkIfTenant($log_group, $goDB)) {
			if($log_group !== "ADMIN") {
				$astDB->where("user_group", $log_group);
			}
			$group_type 				= "Default";
		} else {
			$astDB->where("user_group", $log_group);		
			$group_type					= "Multi-tenant";
		}
			
		$cols 								= array(
			"user_group", 
			"group_name", 
			"forced_timeclock_login"
		);
		
		$select 							= $astDB->get("vicidial_user_groups", NULL, $cols);
		
		if ($astDB->count > 0) {
			foreach($select as $fresults){
				$dataUserGroup[] 			= $fresults['user_group'];
				$dataGroupName[] 			= $fresults['group_name'];
				$dataGroupType[] 			= $group_type;
				$dataForced[] 				= $fresults['forced_timeclock_login'];
			}

			$apiresults 					= array(
				"result" 						=> "success", 
				"user_group" 					=> $dataUserGroup, 
				"group_name" 					=> $dataGroupName, 
				"group_type" 					=> $dataGroupType, 
				"forced_timeclock_login" 		=> $dataForced
			);
		}
	}
?>
