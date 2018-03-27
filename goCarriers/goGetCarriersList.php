<?php
 /**
 * @file 		goGetCarriersList.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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

    
    $limit = $astDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    $groupId = go_get_groupid($goUser, $astDB);
    
	if (!checkIfTenant($groupId, $goDB)) {
        //$ul='';
    } else { 
		//$ul = "WHERE user_group='$groupId'";
		$astDB->where('user_group', $groupId);
	}

   	//$query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group,dialplan_entry FROM vicidial_server_carriers ORDER BY carrier_id LIMIT $limit;";
	$astDB->orderBy('carrier_id', 'desc');
   	$rsltv = $astDB->get('vicidial_server_carriers', $limit, 'carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group,dialplan_entry');

	foreach ($rsltv as $fresults){
		$dataCarrierId[] = $fresults['carrier_id'];
       	$dataCarrierName[] = $fresults['carrier_name'];
		$dataServerIp[] = $fresults['server_ip'];
		$dataProtocol[] = $fresults['protocol'];
		$dataRegistrationString[] = $fresults['registration_string'];
		$dataActive[] = $fresults['active'];
		$dataUserGroup[] = $fresults['user_group'];
		$dataDialPlanEntry[] = $fresults['dialplan_entry'];
   		$apiresults = array("result" => "success", "carrier_id" => $dataCarrierId, "carrier_name" => $dataCarrierName, "server_ip" => $dataServerIp, "protocol" => $dataProtocol, "registration_string" => $dataRegistrationString, "active" => $dataActive, "user_group" => $dataUserGroup, "dialplan_entry" => $dataDialPlanEntry);
	}

?>
