<?php
 /**
 * @file        goDeleteScript.php
 * @brief       API for Deleting Specific Scripts
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author      Jeremiah Sebastian V. Samatra
 * @author      Alexander Jim Abenoja
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

	include_once (__DIR__ . "/goAPI.php");	 
 
	$script_id 											= $astDB->escape(($_REQUEST["script_id"] ?? '')); 
	
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
	} elseif (empty($script_id) || is_null($script_id) ) {
		$apiresults 									= [
			"result" 										=> "Error: Set a value for Script ID."
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
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper((string) $log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}
			
			// check if script ID exists
			$astDB->where("script_id", $script_id);        
			$astDB->getOne("vicidial_scripts", "script_id");
			
			if ($astDB->count > 0) {        
				$astDB->where("script_id", $script_id);
				$getScripts 							= $astDB->getOne("vicidial_scripts", "script_id");

				if($getScripts) {
					$astDB->where("script_id", $script_id);
					$astDB->delete("vicidial_scripts");

					$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Script ID: $script_id", $log_group, $astDB->getLastQuery());
					
					$astDB->where("script_id", $script_id);
					$astDB->delete("go_scripts");

					$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Script ID: $script_id", $log_group, $astDB->getLastQuery());
					
					$data_update 						= [
						"campaign_script" 					=> ""
					];
					
					$astDB->where("campaign_script", $script_id);
					$astDB->update("vicidial_campaigns", $data_update);

					$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Script ID: $script_id", $log_group, $astDB->getLastQuery());

					$apiresults 						= [
						"result" 						=> "success"
					];
				} else {
					$apiresults 						= [
						"result" 							=> "Error: Script doesn't exist."
					];
				}
			} else {
				$err_msg 								= error_handle( "10001", "Insufficient permision" );
				$apiresults 							= [
					"code" 									=> "10001", 
					"result" 								=> $err_msg
				];			
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
