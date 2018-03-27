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

    
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
    
    ### Check carrier_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser, $astDB);

		if (!checkIfTenant($groupId, $goDB)) {
			//$ul = "WHERE carrier_id ='$carrier_id'";
		} else { 
			//$ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}
	
		//$query = "SELECT carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
		$astDB->where('carrier_id', $carrier_id);
		$astDB->orderBy('carrier_id', 'desc');
		$rsltv = $astDB->get('vicidial_server_carriers', 3, 'carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string');
		$countResult = $astDB->getRowCount();
		
		if($countResult > 0) {
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the info of carrier id: $carrier_id", $log_group);
			
			$apiresults = array("result" => "success", "data" => $rsltv);
		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}
?>
