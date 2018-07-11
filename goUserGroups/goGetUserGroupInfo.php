<?php
/**
 * @file 		goGetUserGroupInfo.php
 * @brief 		API to get specific User Group Details
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

	include_once ("goAPI.php");
	
	$log_user 							= $session_user;
	$log_group 							= go_get_groupid($session_user, $astDB); 
	$ip_address 						= $astDB->escape($_REQUEST["log_ip"]);
	
    // POST or GET Variables
    $user_group 						= $astDB->escape($_REQUEST["user_group"]);	
    
    if (!isset($session_user) || is_null($session_user)) {
    	$apiresults 					= array(
			"result" 						=> "Error: Missing Required Parameters."
		);
    } elseif (is_null($user_group)) { 
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for User Group."
		); 
	} else {
		if (!checkIfTenant($log_group, $goDB)) {
			$group_type 				= "Default";
		} else {			
			$astDB->where("user_group", $log_group);				
			$group_type 				= "Multi-tenant";
		}
		
		$cols							= array(
			"user_group", 
			"group_name", 
			"allowed_campaigns", 
			"forced_timeclock_login", 
			"shift_enforcement", 
			"admin_viewable_groups"
		);
		
		$astDB->where("user_group", $user_group);
		$astDB->orderBy("user_group","asc");
		$query 							= $astDB->getOne("vicidial_user_groups", $cols);
		
		$gocols							= array(
			"group_level", 
			"permissions"
		);
		
		$goDB->where("user_group", $user_group);
		$querygo 						= $goDB->getOne("user_access_group", $gocols);		
		$data 							= array_merge($query, $querygo);
		
		if ($astDB->count > 0) {
            $apiresults 				= array(
				"result" 					=> "success", 
				"data" 						=> $data
			);
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: User Group doesn't exist."
			);
		}
	}
?>
