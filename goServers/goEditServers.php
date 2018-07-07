<?php
 /**
 * @file 		goEditServers.php
 * @brief 		API to modify servers
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
	$outbound_calls_per_second 			= $astDB->escape($_REQUEST['outbound_calls_per_second']);
	$vicidial_balance_active 			= $astDB->escape($_REQUEST['vicidial_balance_active']);
	$vicidial_balance_rank 				= $astDB->escape($_REQUEST['vicidial_balance_rank']);
	$local_gmt 							= $astDB->escape($_REQUEST['local_gmt']);
	$generate_vicidial_conf 			= $astDB->escape($_REQUEST['generate_vicidial_conf']);
	$rebuild_conf_files 				= $astDB->escape($_REQUEST['rebuild_conf_files']);
	$rebuild_music_on_hold 				= $astDB->escape($_REQUEST['rebuild_music_on_hold']);
	$recording_web_link 				= $astDB->escape($_REQUEST['recording_web_link']);
	$alt_server_ip 						= $astDB->escape($_REQUEST['alt_server_ip']);
	$external_server_ip 				= $astDB->escape($_REQUEST['external_server_ip']);
	$defActive 							= array("Y","N");
	
    ### Check Server ID if its null or empty
	if ($server_id == null) { 
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Server ID."
		); 
	} elseif ($server_ip == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Server IP"
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 						= error_handle("41006", "active");
		$apiresults 					= array("
			code" 							=> "41006", 
			"result" 						=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} else {					
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		}
		
		$astDB->where("server_id", $server_id);
		$astDB->getOne("servers");
		
		if ($astDB->count > 0) {
		
			$data 						= array(
				"server_description" 		=> $server_description,
				"server_ip"					=> $server_ip, 
				"active"					=> $active, 
				"asterisk_version"			=> $asterisk_version, 
				"max_vicidial_trunks"		=> $max_vicidial_trunks, 
				"local_gmt"					=> $local_gmt,
				"user_group"				=> $user_group,
				"outbound_calls_per_second"	=> $outbound_calls_per_second,
				"vicidial_balance_active"	=> $vicidial_balance_active,
				"vicidial_balance_rank"		=> $vicidial_balance_rank,
				"generate_vicidial_conf"	=> $generate_vicidial_conf,
				"rebuild_conf_files"		=> $rebuild_conf_files,
				"rebuild_music_on_hold"		=> $rebuild_music_on_hold,
				"recording_web_link"		=> $recording_web_link,
				"alt_server_ip"				=> $alt_server_ip,
				"external_server_ip"		=> $external_server_ip				
			);
			
			$astDB->where("server_id", $server_id);
			$astDB->update("servers", $data);		
			
			$log_id 					= log_action($goDB, 'UPDATE', $log_user, $ip_address, "Updated Server ID: $server_id", $log_group, $astDB->getLastQuery());			
			$apiresults 				= array(
				"result" 					=> "success", 
				"data" 						=> $astDB->getLastQuery()
			);
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: Server doesn't exist."
			);
		}
	}

?>
