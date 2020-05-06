<?php
/**
 * @file 		goDeleteUser.php
 * @brief 		API to delete specific User 
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
    $user_ids 											= $_REQUEST['user_id'];
    $action 											= $astDB->escape($_REQUEST['action']);	
	
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
	} elseif (empty($user_ids)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	} elseif ($action == "delete_selected") {
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
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}
		
			$error_count 								= 0;
		
			foreach ($user_ids as $userid) {
				$user_id 								= $userid;
				
				$query 									= $astDB
					->where("user_id", $user_id)
					->getOne("vicidial_users");
				
				if ($astDB->count > 0) {
					$phone_login 						= $query['phone_login'];
					
					$astDB->where("user_id", $user_id) ;
					$astDB->delete("vicidial_users");
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted User: $user_id", $log_group, $astDB->getLastQuery());
					
					$astDB->where("extension", $phone_login);
					$astDB->delete("phones");
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Phone: $phone_login", $log_group, $astDB->getLastQuery());
					
					$goDB->where("userid", $user_id);
					$goDB->delete("users");
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted User: $user_id", $log_group, $goDB->getLastQuery());
					
					$kamDB->where("username", $phone_login);
					$kamDB->delete("subscriber");				
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Subscriber: $phone_login", $log_group, $kamDB->getLastQuery());
				
				} else {
					$error_count 						= 1;
				}
				
				if ($error_count == 0) { 
					$apiresults 						= array(
						"result" 							=> "success"
					); 
				}
				
				if ($error_count == 1) {
					$err_msg 							= error_handle("10010");
					$apiresults 						= array(
						"code" 								=> "10010", 
						"result" 							=> $err_msg, 
						"data" 								=> "$user_ids"
					);
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
