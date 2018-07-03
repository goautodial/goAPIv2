<?php
/**
 * @file 		goGetCarrierInfo.php
 * @brief 		API for specific carrier
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
	if (isset($_REQUEST['limit'])) {
			$limit = $astDB->escape($_REQUEST['limit']);
	} else { $limit = 50; }
    
	if (checkIfTenant($log_group, $goDB)) {
        //$astDB->where('user_group', $log_group);
    }

   	//$query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group,dialplan_entry FROM vicidial_server_carriers ORDER BY carrier_id LIMIT $limit;";
   	$cols = array("carrier_id", "carrier_name", "server_ip", "protocol", "registration_string", "active", "user_group", "dialplan_entry");
	$astDB->orderBy('carrier_id', 'desc');
   	$rsltv = $astDB->get('vicidial_server_carriers', $limit, $cols);

	foreach ($rsltv as $fresults){
		$dataCarrierId[] = $fresults['carrier_id'];
       	$dataCarrierName[] = $fresults['carrier_name'];
		$dataServerIp[] = $fresults['server_ip'];
		$dataProtocol[] = $fresults['protocol'];
		$dataRegistrationString[] = $fresults['registration_string'];
		$dataActive[] = $fresults['active'];
		$dataUserGroup[] = $fresults['user_group'];
		$dataDialPlanEntry[] = $fresults['dialplan_entry'];   		
	}

	$apiresults = array("result" => "success", "carrier_id" => $dataCarrierId, "carrier_name" => $dataCarrierName, "server_ip" => $dataServerIp, "protocol" => $dataProtocol, "registration_string" => $dataRegistrationString, "active" => $dataActive, "user_group" => $dataUserGroup, "dialplan_entry" => $dataDialPlanEntry);

?>
