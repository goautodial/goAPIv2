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
	
	$log_user 						= $session_user;
	$log_group 						= go_get_groupid($session_user, $astDB); 
	$log_ip 						= $astDB->escape($_REQUEST['log_ip']);
	
    ### POST or GET Variables
    $carrier_id 					= $astDB->escape($_REQUEST['carrier_id']);
    
    ### Check Carrier ID if its null or empty
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif ($carrier_id == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Carrier ID."
		);
	} else {		
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}
		
		// check carrier ID if valid
		$astDB->where('carrier_id', $carrier_id);
   		$astDB->getOne('vicidial_server_carriers');

		if ($astDB->count > 0) {
			$astDB->where('carrier_id', $carrier_id);
			$astDB->delete('vicidial_server_carriers');
			$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Carrier ID: $carrier_id", $log_group, $astDB->getLastQuery());
			
			// reload sip.conf
			$astDB->where('generate_vicidial_conf', 'Y');
			$astDB->where('active_asterisk_server', 'Y');
			$astDB->where('server_ip', $carrier_id);
			$astDB->update('servers', array('rebuild_conf_files' => 'Y'));
			
			$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Reloaded sip.conf for: $carrier_id", $log_group, $astDB->getLastQuery());
			$apiresults 				= array(
				"result" 					=> "success"
			);
		} else {
			$apiresults					= array(
				"result" 					=> "Error: Carrier doesn't exist."
			);
		}
	}
?>
