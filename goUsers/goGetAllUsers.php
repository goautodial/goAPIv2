<?php
/**
 * @file 		goGetAllUsers.php
 * @brief 		API to get all User Lists 
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
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
    @include_once ("goAPI.php");
    include_once ("../licensed-conf.php");
	
	$log_user 								= $session_user;
	$log_group 								= go_get_groupid($session_user, $astDB);
	
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 						= array(
			"result" 							=> "Error: Session User Not Defined."
		);
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		}
		
		// get user_level
		$astDB->where("user", $log_user);
		$query_userlevel 					= $astDB->getOne("vicidial_users", "user_level");
		$user_level 						= $query_userlevel["user_level"];
		
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		} else {
			if (strtoupper($log_group) != 'ADMIN') {
				if ($user_level > 8) {
					$astDB->where("user_group", $log_group);
				}
			}		
			
		}
		
		// get users list
		$cols 								= array(
			"user_id", 
			"user", 
			"full_name", 
			"user_level", 
			"user_group", 
			"phone_login", 
			"active"
		);
		
		$astDB->where("user", DEFAULT_USERS, "NOT IN");
		$astDB->where("user_level", 4, "!=");
		$astDB->orderby("user", "asc");
		$query 								= $astDB->get("vicidial_users", NULL, $cols);

		if ($astDB->count > 0) {
			$count 							= 0;
			
			foreach ($query as $fresults) {				
				$cols 						= array(
					"userid", 
					"avatar"
				);
				
				$goDB->where("userid", $fresults["user_id"]);
				$goDB->orderby("userid", "desc");
				$querygo 					= $goDB->get("users", NULL, $cols);	
				
				if ($goDB->count > 0) {
					foreach($querygo as $fresultsgo){
						$dataUserIDgo[] 	= $fresultsgo['userid'];
						$dataAvatar[] 		= $fresultsgo['avatar'];		
					}
				}
				
				$dataUserID[] 				= $fresults['user_id'];
				$dataUser[] 				= $fresults['user'];
				$dataFullName[] 			= $fresults['full_name'];
				$dataUserLevel[] 			= $fresults['user_level'];
				$dataUserGroup[] 			= $fresults['user_group'];
				$dataPhone[] 				= $fresults['phone_login'];
				$dataActive[]				= $fresults['active'];
				
				if (preg_match("/^agent/i", $fresults['user'])) {
					$get_last 				= preg_replace("/[^0-9]/","", $fresults['user']);
					$last_num[] 			= intval($get_last);
				}				
							
			}

			// return data
			$get_last 						= max($last_num);
			$agent_num 						= $get_last + 1;	
				
			$apiresults 					= array(
				"result" 						=> "success", 
				"user_id" 						=> $dataUserID,
				"user_group" 					=> $dataUserGroup, 
				"user" 							=> $dataUser, 
				"full_name" 					=> $dataFullName, 
				"user_level" 					=> $dataUserLevel, 
				"phone_login" 					=> $dataPhone, 
				"active" 						=> $dataActive, 
				"avatar" 						=> $dataAvatar, 
				"useridgo" 						=> $dataUserIDgo, 
				"licensedSeats" 				=> $config["licensedSeats"], 
				"last_count" 					=> $agent_num);	
				
		} else {
			$err_msg 						= error_handle("10010");
			$apiresults 					= array(
				"code" 							=> "10010", 
				"result" 						=> $err_msg
			); 
		}
	}

?>
