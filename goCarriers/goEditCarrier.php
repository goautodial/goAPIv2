<?php
 /**
 * @file 		goEdiCarrier.php
 * @brief 		API for modifying carriers
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Alexander Jim Abenoja
 * @author     	Jeremiah Sebastian Samatra
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
	
    $carrier_id 										= $astDB->escape($_REQUEST['carrier_id']);
    $carrier_name 										= $astDB->escape($_REQUEST['carrier_name']);
    $carrier_description 								= $astDB->escape($_REQUEST['carrier_description']);
    $protocol 											= $astDB->escape($_REQUEST['protocol']);
    $server_ip 											= $astDB->escape($_REQUEST['server_ip']);
    $registration_string 								= $astDB->escape($_REQUEST['registration_string']);
    $account_entry 										= $_REQUEST['account_entry'];
	$dialplan_entry 									= $_REQUEST['dialplan_entry'];
    $globals_string										= $astDB->escape($_REQUEST['globals_string']);
    $active 											= $astDB->escape(strtoupper($_REQUEST['active']));
    //$values 											= $_REQUEST['item'];
   
    ### Default values 
	$defProtocol 										= array( "SIP", "Zap", "IAX2", "EXTERNAL" );	
    $defActive 											= array( "Y", "N" );  

    ### Check carrier ID if its null or empty
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
	} elseif (empty ($carrier_id) || is_null ($carrier_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Server ID not less than 3 characters."
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for active is Y or N only."
		);
	} elseif (!in_array($protocol,$defProtocol) && $protocol != null) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for protocol is SIP, Zap, IAX2 or EXTERNAL only."
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
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										= (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
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
			
			// check if carrier_id exists
			$astDB->where('carrier_id', $carrier_id);
			$carrier_data								= $astDB->getOne("vicidial_server_carriers");

			if ($carrier_data) {	
				$data 									= array(
					'carrier_name' 							=> $carrier_name,
					'carrier_description' 					=> $carrier_description,
					'protocol' 								=> $protocol,
					'server_ip' 							=> $server_ip,
					'active' 								=> $active,
					'registration_string' 					=> $registration_string,
					'account_entry' 						=> $account_entry,
					'dialplan_entry' 						=> $dialplan_entry,
					'globals_string' 						=> $globals_string
				);
				
				$astDB->where('carrier_id', $carrier_id);
				$q_update 								= $astDB->update('vicidial_server_carriers', $data);
				$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Updated the carrier settings for: $carrier_id", $log_group, $astDB->getLastQuery());
				
				if ($q_update) {
					$data 								= array(
						"rebuild_conf_files" 				=> "Y"
					);
					
					$astDB->where("generate_vicidial_conf", "Y");
					$astDB->where("active_asterisk_server", "Y");
					$astDB->where("server_ip", $server_ip);
					$astDB->update("servers", $data);

					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Reloaded sip.conf for: $carrier_id", $log_group, $astDB->getLastQuery());
					
					$apiresults 						= array(
						"result" 							=> "success", 
						"data" 								=> $q_update
					);
				} else{
					$apiresults 						= array(
						"result" 							=> "Error in Saving: It appears something has occured, please consult your GOautodial administrator."
					);
				}
			} else {
				$err_msg 								= error_handle("10001");
				$apiresults 							= array(
					"code" 									=> "10001", 
					"result" 								=> $err_msg
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
