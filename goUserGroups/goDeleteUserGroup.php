<?php
/**
 * @file 		goDeleteUserGroup.php
 * @brief 		API to delete specific User Group
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
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

    include_once (__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */

 
    // POST or GET Variables
	$user_group 										= $astDB->escape(($_REQUEST['user_group'] ?? ''));
	
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI User Not Defined."
		];
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI Password Not Defined."
		];
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= [
			"result" 										=> "Error: Session User Not Defined."
		];
	} elseif (empty($user_group) || is_null($user_group)) { 
		$apiresults 									= [
			"result" 										=> "Error: Set a value for User Group."
		]; 
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
			$tenant										=  (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper((string) $log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}				
			}
			
			$astDB->where("user_group", $user_group);
			$fresults 									= $astDB->getOne("vicidial_user_groups", "user_group");	
		
			if($astDB->count > 0) {
				$dataUserGroup 							= $fresults["user_group"];
							
				if ($dataUserGroup) {
					$astDB->where("user_group", $dataUserGroup);
					$astDB->where("user_group", "ADMIN", "!=");
					$astDB->delete("vicidial_user_groups");				
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $astDB->getLastQuery());

					$goDB->where("user_group", $dataUserGroup);
					$goDB->where("user_group", "ADMIN", "!=");
					$goDB->delete("user_access_group");				
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $goDB->getLastQuery());
					
					$apiresults 						= [
						"result" 							=> "success",
						"data"								=> [$astDB->getLastQuery(), $goDB->getLastQuery()]
					];								
				} else {
					$err_msg 							= error_handle("10010");
					$apiresults 						= [
						"code" 								=> "10010", 
						"result" 								=> $err_msg
					];
					//$apiresults = array("result" => "Error: SQL Query error or not allowed query.");
				}			
			} else {
				$err_msg 								= error_handle("41004", "user_group. Does not exist");
				$apiresults 							= [
					"code" 									=> "41004", 
					"result" 								=> $err_msg
				];
				//$apiresults = array("result" => "Error: User Group doesn't exist.");
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			];		
		}
	}
	
?>
