<?php
    #######################################################
    #### Name: getPauseCodeInfo.php 	               ####
    #### Description: API to get specific Pause Code       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    $agent = get_settings('user', $astDB, $goUser);

    $camp = $_REQUEST['pauseCampID'];
    $pause_code = $_REQUEST['pause_code'];

    if($camp == null || $pause_code == null) {
        $apiresults = array("result" => "Error: Set a value for campaign ID and pause code.");
    } else {
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
            //do nothing
        } else { 
            $astDB->where('user_group', $agent->user_group);  
        }

        $astDB->where('campaign_id', $camp);
        $astDB->where('pause_code', $pause_code);
        $hotkey = $astDB->get('vicidial_pause_codes', null, 'pause_code, pause_code_name, billable, campaign_id');

        if($hotkey){
            foreach($hotkey as $fresults){
                $dataCampID[]   = $fresults['campaign_id'];
                $dataPC[]       = $fresults['pause_code'];
                $dataPCN[]      = $fresults['pause_code_name'];
                $dataBill[]     = $fresults['billable']; 
            }

            $apiresults = array("result" => "success", "campaign_id" => $dataCampID, "pause_code" => $dataPC, "pause_code_name" => $dataPCN, "billable" => $dataBill);
        } else {
            $apiresults = array("result" => "Error: Lead Filter does not exist.");
    	}
    }
?>
