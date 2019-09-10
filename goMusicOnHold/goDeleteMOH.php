<?php
 /**
 * @file 		goDeleteMOH.php
 * @brief 		API for Deleting Music On Hold
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author		Jeremiah Sebastian Samatra
 * @author     	Chris Lomuntad
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
	  
    ### POST or GET Variables
    $moh_id 											= $astDB->escape($_REQUEST['moh_id']);
    
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
	} elseif ($moh_id == null || strlen($moh_id) < 3) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for MOH ID not less than 3 characters."
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
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}

			$astDB->where('moh_id', $moh_id);
			$astDB->getOne('vicidial_music_on_hold');

			if ($astDB->count > 0) {
				$astDB->where('moh_id', $moh_id);
				$astDB->delete('vicidial_music_on_hold');
				$log_id 								= log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where('moh_id', $moh_id);
				$astDB->delete('vicidial_music_on_hold_files');
				$log_id 								= log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());
				
				$apiresults 							= array(
					"result" 								=> "success"
				);
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: MOH doesn't exist."
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
