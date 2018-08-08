<?php
/**
 * @file        goGetAllHotkeys.php
 * @brief       API to get all hokeys of a specific campaign
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

	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB); 

	### POST or GET Variables
	$campaign_id 								= $astDB->escape($_REQUEST["campaign_id"]);

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif(empty($campaign_id)) {
		$apiresults 							= array(
			"result" 								=> "Error: Set a value for Campaign ID."
		);
	} else {
        $cols 									= array(
			"status",
			"hotkey",
			"status_name",
			"selectable",
			"campaign_id"
		);
		
        $astDB->where("campaign_id", $campaign_id);
        $astDB->orderBy("hotkey");
        $hotkeys 								= $astDB->get("vicidial_campaign_hotkeys", null, $cols);

        if ($hotkeys) {
        	foreach($hotkeys as $fresults) {
				$dataStatus[] 					= $fresults["status"];
				$dataHotkey[] 					= $fresults["hotkey"];
				$dataStatusName[] 				= $fresults["status_name"];
				$dataSelectable[] 				= $fresults["selectable"];
				$dataCampaignID[] 				= $fresults["campaign_id"];

				$apiresults 					= array(
					"result" 						=> "success",
					"status" 						=> $dataStatus,
					"hotkey" 						=> $dataHotkey,
					"status_name" 					=> $dataStatusName,
					"selectable" 					=> $dataSelectable,
					"campaign_id" 					=> $dataCampaignID
				);
			}
        } else {
        	$apiresults 						= array(
				"result" 							=> "error"
			);
        }
        
	}
?>
