<?php
/**
 * @file        goGetAllLeadsOnHopper.php
 * @brief       API to get all leads on hopper
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja
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
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
    $campaign_id 									= $astDB->escape($_REQUEST['campaign_id']);
	$limit 											= 100;
    
    // Check campaign_id if its null or empty
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} elseif (in_array($campaign_id, $campaigns)) {
		if (isset($_REQUEST['limit'])) {
			$limit 									= $astDB->escape($_REQUEST['limit']);
		} 
    
		$query 										= "
			SELECT
				vicidial_hopper.lead_id,
				vicidial_list.phone_number,
				vicidial_hopper.state,
				vicidial_list.status,
				vicidial_list.called_count,
				vicidial_hopper.gmt_offset_now,
				vicidial_hopper.hopper_id,
				vicidial_hopper.alt_dial,
				vicidial_hopper.list_id,
				vicidial_hopper.priority,
				vicidial_hopper.source
			FROM 
				vicidial_hopper,vicidial_list
			WHERE 
				vicidial_hopper.lead_id = vicidial_list.lead_id
			AND 
				vicidial_hopper.campaign_id = '$campaign_id'
			ORDER BY 
				vicidial_hopper.hopper_id
			LIMIT $limit;
		";
		
		$rsltv 											= $astDB->rawQuery($query);
		
		if($astDB->count > 0) {  
			//$queryGetDialStatus = "SELECT dial_statuses FROM vicidial_campaigns WHERE campaign_id = '$campaign_id';";
			$astDB->where('campaign_id', $campaign_id);
			$resultQuery 								= $astDB->getOne('vicidial_campaigns', 'dial_statuses');
			$dataDialStatuses[] 						= $resultQuery['dial_statuses'];
		
			foreach ($rsltv as $fresults){
				$dataLeadID[]       					= $fresults['lead_id'];
				$dataPhoneNO[]      					= $fresults['phone_number'];
				$dataState[]        					= $fresults['state'];
				$dataStatus[]       					= $fresults['status'];
				$dataCalledCount[]  					= $fresults['called_count'];
				$dataGMT[]          					= $fresults['gmt_offset_now'];
				$dataHopperID[]     					= $fresults['hopper_id'];
				$dataAltDial[]      					= $fresults['alt_dial'];
				$dataListID[]       					= $fresults['list_id'];
				$dataPriority[]     					= $fresults['priority'];
				$dataSource[]       					= $fresults['source'];
			}
			
			$apiresults 								= array(
				"result"            						=> "success",
				"lead_id"           						=> escapeJsonString($dataLeadID),
				"phone_number"      						=> escapeJsonString($dataPhoneNO),
				"state"             						=> escapeJsonString($dataState),
				"status"            						=> escapeJsonString($dataStatus),
				"called_count"      						=> escapeJsonString($dataCalledCount),
				"gmt_offset_now"    						=> escapeJsonString($dataGMT),
				"hopper_id"         						=> escapeJsonString($dataHopperID),
				"alt_dial"          						=> escapeJsonString($dataAltDial),
				"list_id"           						=> escapeJsonString($dataListID),
				"priority"          						=> escapeJsonString($dataPriority),
				"source"            						=> escapeJsonString($dataSource),
				"camp_dial_status"  						=> escapeJsonString($dataDialStatuses)
			);
		} else {
			$apiresults 								= array(
				"result" 									=> "Error: No records found."
			);
		}
	}
	
?>
