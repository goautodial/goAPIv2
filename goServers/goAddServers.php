<?php
 /**
 * @file 		goAddServers.php
 * @brief 		API to add servers
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
*/

    @include_once ("goAPI.php");
 
	$log_user 							= $session_user;
	$log_group 							= go_get_groupid($session_user, $astDB); 
	$ip_address 						= $astDB->escape($_REQUEST['log_ip']);	
	
    ### POST or GET Variables
	$server_id 							= $astDB->escape($_REQUEST['server_id']);
	$server_description 				= $astDB->escape($_REQUEST['server_description']);
	$server_ip 							= $astDB->escape($_REQUEST['server_ip']);
	$active 							= $astDB->escape($_REQUEST['active']);
	$asterisk_version 					= $astDB->escape($_REQUEST['asterisk_version']);
	$max_vicidial_trunks 				= $astDB->escape($_REQUEST['max_vicidial_trunks']);
	$user_group 						= $astDB->escape($_REQUEST['user_group']);
	$local_gmt 							= "-5.00";
	$defActive 							= array("Y","N");
	
    ### ERROR CHECKING 
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif($server_id == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Server ID not less than 3 characters."
		);
	} elseif ($server_ip == null){
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Server IP"
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 						= error_handle("41006", "active");
		$apiresults 					= array(
			"code" 							=> "41006", 
			"result" 						=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} else {					
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		}
		
		$astDB->where("server_id", $server_id);
		$astDB->orWhere("server_ip", $server_ip);
		$astDB->get("servers");
		
		if ($astDB->count > 0) {
			$apiresults 				= array(
				"result" 					=> "Error: Add failed, Server already already exist!"
			);
		} else {
			$data 						= array(
				"server_id"		 			=> $server_id, 
				"server_description" 		=> $server_description,
				"server_ip"					=> $server_ip, 
				"active"					=> $active, 
				"asterisk_version"			=> $asterisk_version, 
				"max_vicidial_trunks"		=> $max_vicidial_trunks, 
				"local_gmt"					=> $local_gmt,
				"user_group"				=> $user_group
			);
			
			$query 						= $astDB->insert("servers", $data);
			$log_id 					= log_action($goDB, "ADD", $log_user, $ip_address, "Added New Server: $server_id", $log_group, $astDB->getLastQuery());
			
			if($query){
				$apiresults 			= array(
					"result" 				=> "success",
					"data" 					=> $query
				);
			} else {
				$apiresults				= array(
					"result" 				=> "Error: Add failed, check your details"
				);
			}
		} 	
	}

?>
