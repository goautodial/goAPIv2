<?php
 /**
 * @file 		goGetCarrierInfo.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja  <alex@goautodial.com>
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
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    
    ### Check carrier_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			//$astDB->where('user_group', $log_group);
		}
		
		//$query = "SELECT carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
		$cols = array("carrier_id", "carrier_name", "carrier_description", "server_ip", "protocol", "registration_string", "active", "user_group", "account_entry", "dialplan_entry", "registration_string", "globals_string");
		$astDB->where('carrier_id', $carrier_id);
		$astDB->orderBy('carrier_id', 'desc');
		$rsltv = $astDB->getOne('vicidial_server_carriers', $cols);
		
		if(!empty($rsltv)) {
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the info of carrier id: $carrier_id", $log_group);			
			$apiresults = array("result" => "success", "data" => $rsltv);
		} else {
			$apiresults = array("result" => "Error: Empty.");
		}
	}
?>
