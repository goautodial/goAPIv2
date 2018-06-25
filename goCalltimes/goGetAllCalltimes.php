<?php
/**
 * @file        goGetAllCalltimes.php
 * @brief       API to get all Calltime details
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

	include_once ("goAPI.php");
	
	//$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
    $log_ip = $astDB->escape($link, $_REQUEST['log_ip']);

	if (checkIfTenant($log_group, $goDB)) {
		$astDB->where("user_group", $log_group);
	}

	$query = $astDB->get("vicidial_call_times");
	//$query = "SELECT * FROM vicidial_call_times $ul $addedSQL ORDER BY call_time_id;";

	$log_id = log_action($goDB, 'VIEW', $session_user, $log_ip, "Viewed the Calltimes List", $log_group);
	
	foreach ($query as $fresults) {
		$dataCalltimeID[] = $fresults['call_time_id'];
		$dataCalltimeName[] = $fresults['call_time_name'];
		$dataCtDefStart[] = $fresults['ct_default_start'];
		$dataCtDefStop[] = $fresults['ct_default_stop'];
		$dataCtSunStart[] = $fresults['ct_sunday_start'];
		$dataCtSunStop[] = $fresults['ct_sunday_stop'];
		$dataCtMonStart[] = $fresults['ct_monday_start'];
		$dataCtMonStop[] = $fresults['ct_monday_stop'];
		$dataCtTueStart[] = $fresults['ct_tuesday_start'];
		$dataCtTueStop[] = $fresults['ct_tuesday_stop'];
		$dataCtWedStart[] = $fresults['ct_wednesday_start'];
		$dataCtWedStop[] = $fresults['ct_wednesday_stop'];
		$dataCtThuStart[] = $fresults['ct_thursday_start'];
		$dataCtThuStop[] = $fresults['ct_thursday_stop'];
		$dataCtFriStart[] = $fresults['ct_friday_start'];
		$dataCtFriStop[] = $fresults['ct_friday_stop'];
		$dataCtSatStart[] = $fresults['ct_saturday_start'];
		$dataCtSatStop[] = $fresults['ct_saturday_stop'];
		$dataUserGroup[] = $fresults['user_group'];
	}
	$apiresults = array("result" => "success", "call_time_id" => $dataCalltimeID, "call_time_name" => $dataCalltimeName, "ct_default_start" => $dataCtDefStart, "ct_default_stop" => $dataCtDefStop, "ct_sunday_start" => $dataCtSunStart, "ct_sunday_stop" => $dataCtSunStop, "ct_monday_start" => $dataCtMonStart, "ct_monday_stop" => $dataCtMonStop, "ct_tuesday_start" => $dataCtTueStart, "ct_tuesday_stop" => $dataCtTueStop, "ct_wednesday_start" => $dataCtWedStart, "ct_wednesday_stop" => $dataCtWedStop, "ct_thursday_start" => $dataCtThuStart, "ct_thursday_stop" => $dataCtThuStop, "ct_friday_start" => $dataCtFriStart, "ct_friday_stop" => $dataCtFriStop, "ct_saturday_start" => $dataCtSatStart, "ct_saturday_stop" => $dataCtSatStop, "user_group" => $dataUserGroup);
?>
