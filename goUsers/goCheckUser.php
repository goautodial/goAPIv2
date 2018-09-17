<?php
/**
 * @file    	goCheckUser.php
 * @brief     	API to check for existing user data
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
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
    
    include_once ("goAPI.php");
    
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']));			
 
    // POST or GET Variables
	$user 												= $astDB->escape($_REQUEST['user']);
	$phone_login 										= $astDB->escape($_REQUEST['phone_login']);
	$type												= $astDB->escape($_REQUEST['type']);

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
			// User Duplicate Check
			if ($user != NULL && $type == "new") {
				$astDB->where("user", $user);
				$astDB->get("vicidial_users", null, "user");
			
				if ($astDB->count > 0) {
					$apiresults 						= array (
						"result" 							=> "fail", 
						"data" 								=> "There are 1 or more users with that User ID."
					);
				} else {		
					$apiresults 						= array (
						"result" 							=> "success"
					);
				}
			}
			
			// Phone Login Check optional when not null
			if ($phone_login != NULL && $type == "new") {
				$astDB->where("extension", $phone_login);
				$astDB->get("phones", null, "extension");

				if ($astDB->count > 0) {
					$apiresults 						= array (
						"result" 							=> "fail", 
						"data" 								=> "Duplicate phone extension found."
					);
				} else {		
					$apiresults 						= array (
						"result" 							=> "success"
					);
				}		
			}
			
			if ($phone_login != NULL && $type == "edit") {
				$astDB->where("extension", $phone_login);
				$astDB->get("phones", null, "extension");

				if ($astDB->count > 0) {
					$apiresults 						= array (
						"result" 							=> "success"
						//"data" 								=> "Phone extension found." 
					);
				} else {		
					$apiresults 						= array (
						"result" 							=> "fail",
						"data" 								=> "Phone extension not found."
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
