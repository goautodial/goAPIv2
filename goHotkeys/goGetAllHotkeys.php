<?php
/**
 * @file        goGetAllHotkeys.php
 * @brief       API to get all hokeys of a specific campaign
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