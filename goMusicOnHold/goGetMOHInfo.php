<?php
 /**
 * @file 		goGetMOHInfo.php
 * @brief 		API for Getting Music On Hold Info
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

    ### POST or GET Variables
    $moh_id = $astDB->escape($_REQUEST['moh_id']);
	
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
    
    ### Check moh_id if its null or empty
	if($moh_id == null) { 
		$apiresults = array("result" => "Error: Set a value for MOH ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (!checkIfTenant($groupId, $goDB)) {
        	//$ul = "";
    	} else { 
			//$ul = "AND user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}

   		//$query = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' AND moh_id='$moh_id' $ul ORDER BY moh_id LIMIT 1;";
		$astDB->where('remove', 'N');
		$astDB->where('moh_id', $moh_id);
		$astDB->orderBy('moh_id', 'desc');
   		$rsltv = $astDB->getOne('vicidial_music_on_hold', 'moh_id, moh_name, active, random, user_group');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			foreach ($rsltv as $fresults){
				$dataModId[] = $fresults['moh_id'];
				$dataMohName[] = $fresults['moh_name'];
				$dataActive[] = $fresults['active'];
				$dataRandom[] = $fresults['random'];
				$dataUserGroup[] = $fresults['user_group'];
				$apiresults = array("result" => "success", "moh_id" => $dataModId, "moh_name" => $dataMohName, "active" => $dataActive, "random" => $dataRandom, "user_group" => $dataUserGroup);
			}
			
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of Music On-Hold: $moh_id", $log_group);
		} else {
			$apiresults = array("result" => "Error: MOH doesn't exist.");
		}
	}
?>