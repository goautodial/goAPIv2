<?php
    #######################################################
    #### Name: goGetListInfo.php	               ####
    #### Description: API to get specific List	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jermiah Sebastian Samatra         ####
    #### License: AGPLv2                               ####
    #######################################################
    
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