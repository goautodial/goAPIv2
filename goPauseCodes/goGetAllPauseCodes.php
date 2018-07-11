<?php
 /**
 * @file        goGetAllPauseCodes.php
 * @brief       API to get all pause codes in a specific campaign
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noel@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	//$log_ip 						= $astDB->escape($_REQUEST["log_ip"]);
	$campaign_id 					= $astDB->escape($_REQUEST["campaign_id"]);

    // Check campaign_id if its null or empty
    if (!isset($log_user) || is_null($log_user)) {
    	$apiresults 					= array(
			"result" 						=> "Error: Session User dot defined."
		); 
	} elseif (!isset($campaign_id) || is_null($campaign_id)) {
    	$apiresults 					= array(
			"result" 						=> "Error: Campaign ID no defined."
		); 
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where('user_group', $log_group);
		}

		$cols 						= array(
			"campaign_id", 
			"pause_code",
			"pause_code_name",
			"billable"
		);
		
        $astDB->where('campaign_id', $campaign_id);
        $astDB->orderBy('pause_code');
        $pauseCodes 				= $astDB->get('vicidial_pause_codes', NULL, $cols);

        if ($astDB->count > 0) {
			foreach($pauseCodes as $fresults){
				$dataCampID[]   		= $fresults['campaign_id'];
				$dataPC[]       		= $fresults['pause_code'];
				$dataPCN[]      		= $fresults['pause_code_name'];
				$dataBill[]     		= $fresults['billable'];
			}

			$apiresults 				= array(
				"result" 					=> "success", 
				"campaign_id" 				=> $dataCampID, 
				"pause_code" 				=> $dataPC, 
				"pause_code_name" 			=> $dataPCN, 
				"billable" 					=> $dataBill
			);   
		} else {
			$apiresults 				= array(
				"result" 					=> "success" // No Pause Codes available
			);					
		}
	}

?>
