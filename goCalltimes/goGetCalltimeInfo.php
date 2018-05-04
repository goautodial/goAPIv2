<?php
/**
 * @file        getCalltimesInfo.php
 * @brief       API to get specific Calltime details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Warren Ipac Briones   <warren@goautodial.com>
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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

    include_once("../goFunctions.php");
    $call_time_id = $_REQUEST["call_time_id"]; 
	$log_user = $session_user;
	$log_ip = mysqli_real_escape_string($link, $_REQUEST['log_ip']);

    if($call_time_id == null) {
        $apiresults = array("result" => "Error: Set a value for Calltime ID.");
    } else {
		$groupId = go_get_groupid($session_user, $astDB);

		if (checkIfTenant($groupId)) {
			$astDB->where("user_group", $groupId);
		}

		$astDB->where("call_time_id", $call_time_id);
		$results = $astDB->getOne("vicidial_call_times");
		//$query = "SELECT * FROM vicidial_call_times WHERE call_time_id='$call_time_id' $ul $addedSQL ORDER BY call_time_id LIMIT 1;";
		
		if($astDB->count > 0){
			$log_id = log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the info of calltime id: {$call_time_id}", $groupId);
			
			$apiresults = array_merge(array("result" => "success"), $results);
	    } else {
            $apiresults = array("result" => "Error: Calltime does not exist.");
        }
    }
?>
