<?php
/**
 * @file        goGetAllLeadsOnHopper.php
 * @brief       API to get all leads on hopper
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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
  
    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$list_id = $astDB->escape($_REQUEST['list_id']);
    
    $query = "SELECT
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
    FROM vicidial_hopper,vicidial_list
    WHERE vicidial_hopper.lead_id = vicidial_list.lead_id
	AND vicidial_hopper.campaign_id = '$campaign_id'
	ORDER BY vicidial_hopper.hopper_id
    LIMIT 2000;";
    $rsltv = $astDB->rawQuery($query);
	$countResult = $astDB->getRowCount();
    
    if($countResult > 0) {
		//$queryGetDialStatus = "SELECT dial_statuses FROM vicidial_campaigns WHERE campaign_id = '$campaign_id';";
		$astDB->where('campaign_id', $campaign_id);
		$resultQuery = $astDB->getOne('vicidial_campaigns', 'dial_statuses');
		$dataDialStatuses[] = $resultQuery['dial_statuses'];
	
		foreach ($rsltv as $fresults){
			$dataLeadID[]       = $fresults['lead_id'];
			$dataPhoneNO[]      = $fresults['phone_number'];
			$dataState[]        = $fresults['state'];
			$dataStatus[]       = $fresults['status'];
			$dataCalledCount[]  = $fresults['called_count'];
			$dataGMT[]          = $fresults['gmt_offset_now'];
			$dataHopperID[]     = $fresults['hopper_id'];
			$dataAltDial[]      = $fresults['alt_dial'];
			$dataListID[]       = $fresults['list_id'];
			$dataPriority[]     = $fresults['priority'];
			$dataSource[]       = $fresults['source'];
		}
		
		$apiresults = array(
			"result"            => "success",
			"lead_id"           => $dataLeadID,
			"phone_number"      => $dataPhoneNO,
			"state"             => $dataState,
			"status"            => $dataStatus,
			"called_count"      => $dataCalledCount,
			"gmt_offset_now"    => $dataGMT,
			"hopper_id"         => $dataHopperID,
			"alt_dial"          => $dataAltDial,
			"list_id"           => $dataListID,
			"priority"          => $dataPriority,
			"source"            => $dataSource,
			"camp_dial_status"  => $dataDialStatuses
		);
    }else{
        $apiresults = array("result" => "Error: No record found.");
    }
?>