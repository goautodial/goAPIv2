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
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    $ip_address = $astDB->escape($_REQUEST['log_ip']);
    
    ### Check campaign_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
		if (!checkIfTenant($log_group, $goDB)) {
        	//$ul = "WHERE carrier_id='$carrier_id'";
    	} else { 
			//$ul = "WHERE carrier_id='$carrier_id' AND user_group='$log_group'";
			$astDB->where('user_group', $log_group);
		}

   		//$query = "SELECT carrier_id, server_ip FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 1;";
		$astDB->where('carrier_id', $carrier_id);
   		$rsltv = $astDB->getOne('vicidial_server_carriers', 'carrier_id, server_ip');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			$dataCarrierID = $rsltv['carrier_id'];
			$server_ip = $rsltv['server_ip'];

			if(!$dataCarrierID == null) {
				//$deleteQuery = "DELETE FROM vicidial_server_carriers WHERE carrier_id = '$carrier_id';";
				$astDB->where('carrier_id', $carrier_id);
   				$deleteResult = $astDB->delete('vicidial_server_carriers');
				
				//$queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
				$astDB->where('generate_vicidial_conf', 'Y');
				$astDB->where('active_asterisk_server', 'Y');
				$astDB->where('server_ip', $server_ip);
				$astDB->update('servers', array('rebuild_conf_files' => 'Y'));

				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Carrier ID: $carrier_id", $log_group, $deleteResult);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Carrier doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}//end
?>
