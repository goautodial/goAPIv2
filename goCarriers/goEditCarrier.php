<?php
 /**
 * @file 		goEditCarrier.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @copyright	Alex
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

    @include_once ("goAPI.php");
 
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    $carrier_name = $astDB->escape($_REQUEST['carrier_name']);
    $carrier_description = $astDB->escape($_REQUEST['carrier_description']);
    $protocol = $astDB->escape($_REQUEST['protocol']);
    $server_ip = $astDB->escape($_REQUEST['server_ip']);
    $registration_string = $astDB->escape($_REQUEST['registration_string']);
    $account_entry = $_REQUEST['account_entry'];
	$dialplan_entry = $_REQUEST['dialplan_entry'];
    $globals_string = $astDB->escape($_REQUEST['globals_string']);
    $active = $astDB->escape(strtoupper($_REQUEST['active']));
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
    //$values = $_REQUEST['item'];
   
    ### Default values 
    $defActive = array("Y","N");
    $defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
    
	$log_user 	= $session_user;
	$log_group 	= go_get_groupid($session_user, $astDB);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);    

	if($carrier_id == null) {
		$apiresults = array("result" => "Error: Set a value for Carrier ID.");
	} elseif(!in_array($active,$defActive) && $active != null) {
		$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif(!in_array($protocol,$defProtocol) && $protocol != null) {
		$apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2 or EXTERNAL only");
	} else {
		if (!checkIfTenant($log_user, $goDB)) {
			//$ul = "WHERE carrier_id ='$carrier_id'";
		} else {
			//$ul = "WHERE carrier_id ='$carrier_id' AND user_group='$log_user'";
			$astDB->where('user_group', $log_user);
		}

		//$query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
		//$astDB->where('carrier_id', $carrier_id);
		//$astDB->orderBy('carrier_id', 'desc');
		//$fresults = $astDB->get('vicidial_server_carriers');				
		//$countResult = $astDB->getRowCount();

		//if($countResult > 0) {		
			/*foreach ($fresults as $fresult){
				if (is_null($user_group)) { $user_group = $fresult["user_group"]; } 
				if (is_null($protocol)) { $protocol = $fresult["protocol"]; }
				if (is_null($carrier_description)) { $carrier_description = $fresult["carrier_description"]; }
				if (is_null($registration_string)) { $registration_string = $fresult["registration_string"]; }
				if (is_null($account_entry)) { $account_entry = $fresult["account_entry"]; }
				if (is_null($globals_string)) { $globals_string = $fresult["globals_string"]; }
				if (is_null($dialplan_entry)) { $dialplan_entry = $fresult["dialplan_entry"]; }
			}*/
			
			$updateData = array(
				'carrier_name' => $carrier_name,
				'carrier_description' => $carrier_description,
				'protocol' => $protocol,
				'server_ip' => $server_ip,
				'active' => $active,
				'registration_string' => $registration_string,
				'account_entry' => $account_entry,
				'dialplan_entry' => $dialplan_entry,
				'globals_string' => $globals_string
			);
			
			$astDB->where('carrier_id', $carrier_id);
			$result = $astDB->update('vicidial_server_carriers', $updateData);

			if($result) {			
				$astDB->where('generate_vicidial_conf', 'Y');
				$astDB->where('active_asterisk_server', 'Y');
				$astDB->where('server_ip', $server_ip);
				$resultNew = $astDB->update('servers', array('rebuild_conf_files' => 'Y'));

				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Updated the carrier settings for $carrier_id", $log_group, $result);
				$apiresults = array("result" => "success", "data" => $result);
			}else{
				$apiresults = array("result" => "Error in Saving: It appears something has occured, please consult the system administrator", "data" => $result);
			}
		//}
	}

?>
