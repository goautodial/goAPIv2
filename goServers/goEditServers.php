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

    include_once ("goAPI.php");
 
    ### POST or GET Variables
	$server_id 											= $astDB->escape($_REQUEST['server_id']);
	$server_description 								= $astDB->escape($_REQUEST['server_description']);
	$server_ip 											= $astDB->escape($_REQUEST['server_ip']);
	$active 											= $astDB->escape($_REQUEST['active']);
	$asterisk_version 									= $astDB->escape($_REQUEST['asterisk_version']);
	$max_vicidial_trunks 								= $astDB->escape($_REQUEST['max_vicidial_trunks']);
	$user_group 										= $astDB->escape($_REQUEST['user_group']);	
	$outbound_calls_per_second 							= $astDB->escape($_REQUEST['outbound_calls_per_second']);
	$vicidial_balance_active 							= $astDB->escape($_REQUEST['vicidial_balance_active']);
	$vicidial_balance_rank 								= $astDB->escape($_REQUEST['vicidial_balance_rank']);
	$local_gmt 											= $astDB->escape($_REQUEST['local_gmt']);
	$generate_vicidial_conf 							= $astDB->escape($_REQUEST['generate_vicidial_conf']);
	$rebuild_conf_files 								= $astDB->escape($_REQUEST['rebuild_conf_files']);
	$rebuild_music_on_hold 								= $astDB->escape($_REQUEST['rebuild_music_on_hold']);
	$recording_web_link 								= $astDB->escape($_REQUEST['recording_web_link']);
	$alt_server_ip 										= $astDB->escape($_REQUEST['alt_server_ip']);
	$external_server_ip 								= $astDB->escape($_REQUEST['external_server_ip']);
	$defActive 											= array("Y","N");
	
    ### Check Server ID if its null or empty
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty ($server_id) || is_null ($server_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Server ID not less than 3 characters."
		);
	} elseif (empty ($server_ip) || is_null ($server_ip)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Server IP"
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 										= error_handle("41006", "active");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} else {					
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
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
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
		
			$astDB->where("server_id", $server_id);
			$astDB->getOne("servers");
			
			if ($astDB->count > 0) {
			
				$data 									= array(
					"server_description" 					=> $server_description,
					"server_ip"								=> $server_ip, 
					"active"								=> $active, 
					"asterisk_version"						=> $asterisk_version, 
					"max_vicidial_trunks"					=> $max_vicidial_trunks, 
					"local_gmt"								=> $local_gmt,
					"user_group"							=> $user_group,
					"outbound_calls_per_second"				=> $outbound_calls_per_second,
					"vicidial_balance_active"				=> $vicidial_balance_active,
					"vicidial_balance_rank"					=> $vicidial_balance_rank,
					"generate_vicidial_conf"				=> $generate_vicidial_conf,
					"rebuild_conf_files"					=> $rebuild_conf_files,
					"rebuild_music_on_hold"					=> $rebuild_music_on_hold,
					"recording_web_link"					=> $recording_web_link,
					"alt_server_ip"							=> $alt_server_ip,
					"external_server_ip"					=> $external_server_ip				
				);
				
				$astDB->where("server_id", $server_id);
				$astDB->update("servers", $data);		
				
				$log_id 								= log_action($goDB, 'UPDATE', $log_user, $log_ip, "Updated Server ID: $server_id", $log_group, $astDB->getLastQuery());			
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"data" 									=> $astDB->getLastQuery()
				);
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: Server doesn't exist."
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
