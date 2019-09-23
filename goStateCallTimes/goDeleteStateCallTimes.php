<?php
 /**
 * @file 		goDeleteStateCallTime.php
 * @brief 		API for State Calltimes
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
	$state_call_time_id = $astDB->escape($_REQUEST['state_call_time_id']);
    
    ### Check Voicemail ID if its null or empty
	if($state_call_time_id == null) { 
		$apiresults = array("result" => "Error: Set a value for State Call Time ID."); 
	} else {
		$groupId = go_get_groupid($goUser);

		if (!checkIfTenant($groupId)) {
			//$ul = "";
		} else {
			//$ul = "AND user_group='$groupId'";
			//$addedSQL = "WHERE user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}

		
   		//$queryOne = "SELECT state_call_time_id FROM vicidial_state_call_times $ul where state_call_time_id='".mysqli_escape_string($state_call_time_id)."';";
		$astDB->where('state_call_time_id', $state_call_time_id);
   		$rsltvOne = $astDB->get('vicidial_state_call_times');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			//$deleteQuery = "DELETE FROM vicidial_state_call_times WHERE state_call_time_id= '$state_call_time_id';";
			$astDB->where('state_call_time_id', $state_call_time_id);
			$astDB->delete('vicidial_state_call_times');
			
			$log_id = log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted State Call Time: $state_call_time_id", $log_group, $astDB->getLastQuery());
			$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: State Call Menu doesn't exist.");
		}
	}//end
?>