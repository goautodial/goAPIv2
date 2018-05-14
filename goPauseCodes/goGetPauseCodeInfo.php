<?php
 /**
 * @file        goGetPauseCodeInfo.php
 * @brief       API to get specific Pause Code
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
