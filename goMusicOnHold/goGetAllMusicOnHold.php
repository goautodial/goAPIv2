<?php
 /**
 * @file 		goGetAllMusicOnHold.php
 * @brief 		API for Getting All Music On Hold
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho
 * @author		Jeremiah Sebastian Samatra
 * @author     	Chris Lomuntad
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
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
	if (isset($_REQUEST['limit'])) {
			$limit = $astDB->escape($_REQUEST['limit']);
	} else { $limit = 50; }
	
	if (!checkIfTenant($log_group, $goDB)) {
		//$ul='';
	} else {
		//$ul = "AND user_group='$log_group'";
		$astDB->where('user_group', $log_group);
	}

   	//$query = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' $ul ORDER BY moh_id LIMIT $limit;";
   	$cols = array("moh_id", "moh_name", "active", "random", "user_group");
	$astDB->where("remove", "N");
   	$rsltv = $astDB->get("vicidial_music_on_hold", $limit, $cols);

	foreach ($rsltv as $fresults){
		$dataModId[] = $fresults['moh_id'];
       	$dataMohName[] = $fresults['moh_name'];
		$dataActive[] = $fresults['active'];
		$dataRandom[] = $fresults['random'];
		$dataUserGroup[] = $fresults['user_group'];
   		$apiresults = array("result" => "success", "moh_id" => $dataModId, "moh_name" => $dataMohName, "active" => $dataActive, "random" => $dataRandom, "user_group" => $dataUserGroup);
	}
?>
