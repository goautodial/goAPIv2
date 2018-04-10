<?php
    #######################################################
    #### Name: getAllPauseCodes.php 	               ####
    #### Description: API to get all Pause Code in a specific campaign      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    $agent = get_settings('user', $astDB, $goUser);

	$camp = $_REQUEST['pauseCampID'];
    $groupId = go_get_groupid($goUser);

    if(empty($camp)) {
        $apiresults = array("result" => "Error: Set a value for Campaign ID.");
    } else {
        if (!checkIfTenant($groupId)) {
            //do nothing
        } else { 
            $astDB->where('user_group', $agent->user_group);  
        }

        $astDB->where('campaign_id', $camp);
        $astDB->orderBy('pause_code');
        $pauseCodes = $astDB->get('vicidial_pause_codes', null, 'campaign_id, pause_code,pause_code_name,billable');

        foreach($pauseCodes as $fresults){
			$dataCampID[]   = $fresults['campaign_id'];
			$dataPC[]       = $fresults['pause_code'];
			$dataPCN[]      = $fresults['pause_code_name'];
			$dataBill[]     = $fresults['billable'];
		}

        $apiresults = array("result" => "success", "campaign_id" => $dataCampID, "pause_code" => $dataPC, "pause_code_name" => $dataPCN, "billable" => $dataBill);
	}

?>
