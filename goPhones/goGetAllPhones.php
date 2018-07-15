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

	$log_user 								= $session_user;
	$log_group 								= go_get_groupid($session_user, $astDB);
	$limit 									= 1000;
	
	
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 						= array(
			"result" 							=> "Error: Session User Not Defined."
		);
	} else {		
		if (isset($_REQUEST['limit'])) {
			$limit 							= $astDB->escape($_REQUEST['limit']);
		} 
		
		// generate random phone login
		$x 									= 0;
		$y 									= 0;
		$phone_login 						= '';
		while($x == $y){
			$random_digit 					= mt_rand(1000000000, 9999999999);
			$astDB->where("phone_login", $random_digit);
			$astDB->getOne("vicidial_users", "phone_login");
			//$check_existing_phonelogins_query = "SELECT phone_login FROM vicidial_users WHERE phone_login = '$random_digit';";
			
			if($astDB->count < 1){
				$y 							= 1;
				$phone_login 				= $random_digit;
			}
		}
	   	
		
		if (checkIfTenant($log_group, $astDB)) {
			$astDB->where("user_group", $log_group);
		} else {
			if ($log_group != 'ADMIN') {
				$astDB->where("user_group", $log_group);
			}
		}
		
		$col 								= array(
			"extension", 
			"protocol", 
			"server_ip", 
			"active", 
			"messages", 
			"old_messages"
		);
		
	   	$getQuery 							= $astDB->get("phones", $limit, $col);
		
		if ($astDB->count > 0) {
			foreach ($getQuery as $fresults){
				$dataExtension[] 			= $fresults['extension'];
				$dataProtocol[] 			= $fresults['protocol'];
				$dataServerIp[] 			= $fresults['server_ip'];
				$dataActive[] 				= $fresults['active'];
				$dataMessages[] 			= $fresults['messages'];
				$dataOldMessages[] 			= $fresults['old_messages'];
			}
			
			$apiresults 					= array(
				"result" 						=> "success", 
				"extension" 					=> $dataExtension, 
				"protocol" 						=> $dataProtocol, 
				"server_ip" 					=> $dataServerIp, 
				"active" 						=> $dataActive, 
				"messages" 						=> $dataMessages, 
				"old_messages" 					=> $dataOldMessages, 
				"available_phone" 				=> $phone_login
			);		
		}

	}
		
?>
