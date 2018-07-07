<?php
 /**
 * @file 		goGetCarrierInfo.php
 * @brief 		API for getting carrier details
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Alexander Jim Abenoja
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
	
	$log_user 							= $session_user;
	$log_group 							= go_get_groupid($session_user, $astDB); 
	$ip_address 						= $astDB->escape($_REQUEST["log_ip"]);	
	
    ### POST or GET Variables
    $carrier_id 						= $astDB->escape($_REQUEST["carrier_id"]);
    
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
		
		$astDB->where("carrier_id", $carrier_id);
		$astDB->orderBy("carrier_id", "desc");
		
		$rsltv 							= $astDB->getOne("vicidial_server_carriers", $cols);
		//$log_id 						= log_action($goDB, "VIEW", $log_user, $ip_address, "Viewed carrier ID: $carrier_id", $astDB->getLastQuery());
		
		if ($astDB->count > 0) {						
			$apiresults 				= array(
				"result" 					=> "success",
				"data"						=> $rsltv
			);
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: Empty."
			);
		}
	}
	
?>
