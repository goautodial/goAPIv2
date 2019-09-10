<?php
/**
 * @file        goGetAllPhones.php
 * @brief       API for get get all Phone Details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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

	$limit 												= (isset($_REQUEST['limit']) ? $astDB->escape($_REQUEST['limit']) : 1000);
	
	
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
			// generate random phone login
			$x 											= 0;
			$y 											= 0;
			$phone_login 								= '';
			
			while ($x == $y) {
				$random_digit 							= mt_rand(1000000000, 9999999999);
				$astDB->where("phone_login", $random_digit);
				$astDB->getOne("vicidial_users", "phone_login");
				//$check_existing_phonelogins_query = "SELECT phone_login FROM vicidial_users WHERE phone_login = '$random_digit';";
				
				if($astDB->count < 1){
					$y 									= 1;
					$phone_login 						= $random_digit;
				}
			}
			
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
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
		
			$col 										= array(
				"extension", 
				"protocol", 
				"server_ip", 
				"active", 
				"messages", 
				"old_messages"
			);
			
			$getQuery 									= $astDB->get("phones", $limit, $col);
			
			if ($astDB->count > 0) {
				foreach ($getQuery as $fresults){
					$dataExtension[] 					= $fresults['extension'];
					$dataProtocol[] 					= $fresults['protocol'];
					$dataServerIp[] 					= $fresults['server_ip'];
					$dataActive[] 						= $fresults['active'];
					$dataMessages[] 					= $fresults['messages'];
					$dataOldMessages[] 					= $fresults['old_messages'];
				}
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"extension" 							=> $dataExtension, 
					"protocol" 								=> $dataProtocol, 
					"server_ip" 							=> $dataServerIp, 
					"active" 								=> $dataActive, 
					"messages" 								=> $dataMessages, 
					"old_messages" 							=> $dataOldMessages, 
					"available_phone" 						=> $phone_login
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
