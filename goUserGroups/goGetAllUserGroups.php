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
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level,user_group");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		$user_group										= $fresults["user_group"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			//$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			$tenant                                     = ($userlevel < 9 && $log_group !== "ADMIN") ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$group_type								= "Multi-tenant";
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}	
				
				$group_type 							= "Default";				
			}			
			
			$cols 										= array(
				"user_group", 
				"group_name", 
				"forced_timeclock_login"
			);
			
			$select 									= $astDB->get("vicidial_user_groups", NULL, $cols);
			
			if ($astDB->count > 0) {
				foreach($select as $fresults){
					$dataUserGroup[] 					= $fresults['user_group'];
					$dataGroupName[] 					= $fresults['group_name'];
					$dataGroupType[] 					= $group_type;
					$dataForced[] 						= $fresults['forced_timeclock_login'];
				}

				$apiresults 							= array(
					"result" 								=> "success", 
					"user_group" 							=> $dataUserGroup, 
					"group_name" 							=> $dataGroupName, 
					"group_type" 							=> $dataGroupType, 
					"forced_timeclock_login" 				=> $dataForced
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
