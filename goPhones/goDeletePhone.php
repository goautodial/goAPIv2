<?php
/**
 * @file 		goDeletePhone.php
 * @brief 		API to delete specific Phone 
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
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
    
    include_once("goAPI.php");
    
    // POST or GET Variables
    $extensions 										= $_REQUEST['extension'];
	$action 											= $astDB->escape($_REQUEST['action']);
    
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
	} elseif (empty($extensions) || is_null($extensions)) { 
		$apiresults										= array(
			"result" 										=> "Error: Set a value for EXTEN ID."
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
			if ($action == "delete_selected") {
				$error_count 							= 0;
				foreach ($extensions as $extension) {
					$phone_login 						= $extension;
					
					// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
					// every time we need to filter out requests
					$tenant								=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
					
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
					
					$astDB->where("extension", $phone_login);
					$astDB->getOne("phones");
					
					if($astDB->count > 0) {				
						$astDB->where("extension", $phone_login);
						$astDB->delete("phones");
						
						$log_id 						= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Phone: $phone_login", $log_group, $astDB->getLastQuery());
						
						$kamDB->where("username", $phone_login);
						$kamDB->delete("subscriber");
						
						$log_id 						= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Phone: $phone_login", $log_group, $kamDB->getLastQuery());
					} else {
						$error_count 					= 1;
					}
					
					if ($error_count == 0) { 
						$apiresults 					= array(
							"result" 						=> "success"
						); 
					}
					
					if ($error_count == 1) {
						$err_msg 						= error_handle("10010");
						$apiresults 					= array(
							"code" 							=> "10010", 
							"result" 						=> $err_msg, 
							"data" 							=> "$extensions"
						);
					}
				}
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
