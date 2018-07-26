<?php
 /**
 * @file 		goGetRealtimeCallsMonitoring.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Jericho James Milo
 * @author     	Demian Lizandro A. Biscocho
 * @author     	Chris Lomuntad
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
 
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	//$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
	
    // ERROR CHECKING 
	if (!isset($log_user) || is_null($log_user)){
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {	
		$cols										= array(
			"status",
			"phone_number",
			"call_type",
			"UNIX_TIMESTAMP(call_time) as call_time",
			"vac.campaign_id"
		);
		
		$table 										= "
			vicidial_auto_calls as vac, 
			vicidial_campaigns as vc, 
			vicidial_inbound_groups as vig
		";
		
		$astDB->where("vac.campaign_id", $campaigns, "IN");
		$astDB->groupBy("status,call_type,phone_number");		
		$rsltv										= $astDB->get($table, NULL, $cols);
			
		//echo "<pre>";
		//var_dump($astDB->getLastQuery());
		
		if($astDB->count > 0) {
			$data 									= array();
			
			foreach ($rsltv as $fresults){       
				array_push($data, $fresults);
			}
			
			$apiresults 							= array(
				"result" 								=> "success", 
				//"query"									=> $astDB->getLastQuery(),
				"data" 									=> $data
			);
		}
		
		$apiresults 								= array(
			"result" 									=> "success", 
			"data" 										=> 0
		);		
	}
    
?>
