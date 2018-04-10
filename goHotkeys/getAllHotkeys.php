<?php
	####################################################
	#### Name: getAllHotkeys.php                    ####
	#### Description: API to add new list           ####
	#### Version: 0.9                               ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
	#### Written by: Noel Umandap                   ####
	#### License: AGPLv2                            ####
	####################################################
	$agent = get_settings('user', $astDB, $goUser);
	
	$camp = $_REQUEST['hotkeyCampID'];
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
        $astDB->orderBy('hotkey');
        $hotkeys = $astDB->get('vicidial_campaign_hotkeys', null, 'status,hotkey,status_name,selectable,campaign_id');

        if($hotkeys){
        	foreach($hotkeys as $fresults){
				$dataStatus[] 		= $fresults['status'];
				$dataHotkey[] 		= $fresults['hotkey'];
				$dataStatusName[] 	= $fresults['status_name'];
				$dataSelectable[] 	= $fresults['selectable'];
				$dataCampaignID[] 	= $fresults['campaign_id'];

				$apiresults = array(
					"result" 		=> "success",
					"status" 		=> $dataStatus,
					"hotkey" 		=> $dataHotkey,
					"status_name" 	=> $dataStatusName,
					"selectable" 	=> $dataSelectable,
					"campaign_id" 	=> $dataCampaignID
				);
			}
        }else{
        	$apiresults = array("result" => "error");
        }
        
	}
?>