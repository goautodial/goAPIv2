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

	### POST or GET Variables
	$campaign_id 										= $astDB->escape($_REQUEST["campaign_id"]);

	// ERROR CHECKING 
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Campaign ID."
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			$cols 									= array(
				"status",
				"hotkey",
				"status_name",
				"selectable",
				"campaign_id"
			);
			
			$hotkeys 								= $astDB
				->where("campaign_id", $campaign_id)
				->orderBy("hotkey")
				->get("vicidial_campaign_hotkeys", null, $cols);

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
			
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
