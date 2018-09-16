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
    
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
 
    // POST or GET Variables
	$user 											= $astDB->escape($_REQUEST['user']);
	$phone_login 									= $astDB->escape($_REQUEST['phone_login']);

    // Error Checking
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array (
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {
		if ($user != NULL && $phone_login != NULL) {
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
		
		// User Duplicate Check
		if ($user != NULL && $phone_login == NULL) {
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
		if ($phone_login != NULL && $user == NULL) {
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
	}		
	
?>
