<?php
 /**
 * @file 		goDeleteCarrier.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (c) GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
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
	
    $carrier_id 										= $astDB->escape($_REQUEST["carrier_id"]);
    
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
				
			// check carrier ID if valid
			$astDB->where('carrier_id', $carrier_id);
			$astDB->getOne('vicidial_server_carriers');

			if ($astDB->count > 0) {
				$astDB->where('carrier_id', $carrier_id);
				$astDB->delete('vicidial_server_carriers');
				$log_id 								= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Carrier ID: $carrier_id", $log_group, $astDB->getLastQuery());
				
				// reload sip.conf
				$astDB->where('generate_vicidial_conf', 'Y');
				$astDB->where('active_asterisk_server', 'Y');
				$astDB->where('server_ip', $carrier_id);
				$astDB->update('servers', array('rebuild_conf_files' => 'Y'));
				
				$log_id 								= log_action($goDB, 'DELETE', $log_user, $log_ip, "Reloaded sip.conf for: $carrier_id", $log_group, $astDB->getLastQuery());
				
				$apiresults 							= array(
					"result" 								=> "success"
				);
			} else {
				$apiresults								= array(
					"result" 								=> "Error: Carrier doesn't exist."
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
