<?php
 /**
 * @file        goDeletePauseCode.php
 * @brief       API to delete specific pause code
 * @copyright   Copyright (c) 2018 GOautodial Inc.
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
 
	$log_user 								= $session_user;
	$log_group 								= go_get_groupid($session_user, $astDB); 
	$log_ip 								= $astDB->escape($_REQUEST["log_ip"]);
	
	### POST or GET Variables
	$campaign_id		 					= $astDB->escape($_REQUEST['pauseCampID']);
	$pause_code 							= $astDB->escape($_REQUEST['pause_code']);
    
    ### Check Voicemail ID if its null or empty
	if($campaign_id == null || $pause_code == null) { 
		$apiresults 						= array(
			"result" 							=> "Error: Set a value for Campaign ID and Pause Code."
		); 
	} else {
		$cols 								= array(
			"pause_code", 
			"campaign_id"
		);
		
        $astDB->where('campaign_id', $campaign_id);
        $astDB->where('pause_code', $pause_code);
        $checkPC 							= $astDB->get('vicidial_pause_codes', null, $cols);

		if ($checkPC) {
			$astDB->where('campaign_id', $campaign_id);
			$astDB->where('pause_code', $pause_code);
			$astDB->delete('vicidial_pause_codes');

			$log_id = log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Pause Code $pause_code from Campaign ID $campaign_id", $log_group, $astDB->getLastQuery());
			$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: Pause Code doesn't exist.");
		}
	}//end
?>
