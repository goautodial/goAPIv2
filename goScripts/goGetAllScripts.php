<?php
 /**
 * @file 		goGetAllScripts.php
 * @brief 		API to get all scripts
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
 * @author     	Alexander Jim Abenoja
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
				if (strtoupper($log_group) !== 'ADMIN') {
					//if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					//}
				} else {
					$astDB->where('script_id', '^script', 'REGEXP');
				}
			}
		
			// getting script count
			$astDB->orderBy('script_id', 'desc');
			$resultGetScript							= $astDB->getOne('vicidial_scripts', 'script_id');
			
			// condition
			if ($resultGetScript) {
				if ( preg_match("/^script/i", $resultGetScript['script_id']) ) {
					$get_last_count 					= str_replace("script", "", $resultGetScript['script_id']);
					$last_pl[] 							= intval($get_last_count);
				} else {
					$get_last_count 					= str_replace("$log_group", "", $resultGetScript['script_id']);
					$last_pl[] 							= intval($get_last_count);
				}

				// return data
				$script_num 							= max($last_pl);
				$script_num 							= $script_num + 1;
				
				if ($script_num < 100) {
					if ($script_num < 10) {
						$script_num			 			= "00".$script_num;
					} else {
						$script_num 					= "0".$script_num;
					}
				}
				
				if ($log_group !== "ADMIN") {
					$script_num 						= "$log_group".$script_num;
				}else{
					$script_num 						= "script".$script_num;
				}
			} else {
				// return data
				if ($log_group !== "ADMIN") {
					$script_num 						= "{$log_group}001";
				}else{
					$script_num 						= "script001";
				}
			}
				
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					//if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					//}
				}					
			}
			
			$scripts 									= $astDB->get('vicidial_scripts');
			
			if ($astDB->count > 0) {
				foreach ($scripts as $script) {
					$dataScriptID[] 					= $script['script_id'];
					$dataScriptName[] 					= $script['script_name'];
					$dataActive[] 						= $script['active'];
					$dataUserGroup[] 					= $script['user_group'];
				}		
			} 
			
			$apiresults 								= array(
				"result" 									=> "success",
				"script_id" 								=> $dataScriptID,
				"script_name" 								=> $dataScriptName,
				"active" 									=> $dataActive,
				"user_group" 								=> $dataUserGroup,
				"script_count" 								=> $script_num
			);
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
