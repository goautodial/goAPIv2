<?php
 /**
 * @file        goDeletePauseCode.php
 * @brief       API to delete specific pause code
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
    ### POST or GET Variables
	$camp = $_REQUEST['pauseCampID'];
	$code = $_REQUEST['pause_code'];
	$ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
	if($camp == null || $code == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID and Pause Code."); 
	} else {
        $astDB->where('campaign_id', $camp);
        $astDB->where('pause_code', $code);
        $checkPC = $astDB->get('vicidial_pause_codes', null, 'pause_code, campaign_id');

		if($checkPC) {
                $astDB->where('campaign_id', $camp);
                $astDB->where('pause_code', $code);
                $astDB->delete('vicidial_pause_codes');
                $deleteQuery = $astDB->getLastQuery();

				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Pause Code $code from Campaign ID $camp", $log_group, $deleteQuery);
				$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: Pause Code doesn't exist.");
		}
	}//end
?>
