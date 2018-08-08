<?php
/**
 * @file        goDeleteHotkey.php
 * @brief       API to delete a specific hotkey/s
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

    include_once ("goAPI.php");
 
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB); 
	$log_ip 									= $astDB->escape($_REQUEST["log_ip"]); 
	
	### POST or GET Variables
	$campaign_id 								= $astDB->escape($_REQUEST["campaign_id"]);	
    $hotkeys 									= $astDB->escape($_REQUEST["hotkey"]);
    
    ### Check Campaign ID if its null or empty
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || empty($hotkeys)) { 
		$apiresults 							= array(
			"result" 								=> "Error: Set a value for Campaign ID and Hotkey."
		); 
	} else {
		$cols 									= array(
			"campaign_id", 
			"hotkey"
		);
	
        $astDB->where("campaign_id", $campaign_id);
        $astDB->where("hotkey", $hotkeys);
        $checkPC								= $astDB->get("vicidial_campaign_hotkeys", null, $cols);
        
		if ($checkPC) {
			$astDB->where("campaign_id", $campaign_id);
			$astDB->where("hotkey", $hotkeys);
			$astDB->delete("vicidial_campaign_hotkeys");

			$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Hotkey: $hotkeys from Campaign ID $campaign_id", $log_group, $astDB->getLastQuery());
			
			$apiresults 						= array(
				"result" 							=> "success"
			);
		} else {
			$apiresults 						= array(
				"result" 							=> "Error: Hotkey doesn't exist."
			);
		}
	}
?>
