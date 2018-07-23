<?php
 /**
 * @file 		goGetAllDialStatuses.php
 * @brief 		API for Dial Status
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
	
	$hotkeys_only 									= $astDB->escape($_REQUEST['hotkeys_only']);
	$campaign_id 									= $astDB->escape($_REQUEST['campaign_id']);
	
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {
		if ($hotkeys_only === "1") {
			$astDB->where("selectable", "Y");
		}
		
		if (strlen($hotkeys_only) > 0 && strlen($campaign_id) > 0) {
			$cols 									= array(
				"status", 
				"status_name"
			);
			
			$astDB->where("campaign_id", $campaign_id);
			$astDB->orderBy("status", "desc");			
			$rsltv 									= $astDB->get("vicidial_campaign_statuses", NULL, $cols);			
					
			if ($astDB->count > 0) {
				foreach ($rsltv as $fresults){
					$dataStatus[] 					= $fresults['status'];
					$dataStatusName[] 				= $fresults['status_name'];
				}		
			}
			

		}
		
		$cols 										= array(
			"status", 
			"status_name"
		);
		
		$astDB->orderBy("status", "desc");			
		$rsltv 										= $astDB->get("vicidial_statuses", NULL, $cols);
		
		if ($astDB->count > 0) {
			foreach ($rsltv as $fresults){
				$dataStatus[] 						= $fresults['status'];
				$dataStatusName[] 					= $fresults['status_name'];
			}
			
			$apiresults 							= array(
				"result" 								=> "success",
				"status" 								=> $dataStatus,
				"status_name" 							=> $dataStatusName
			);		
		}
	}

?>
