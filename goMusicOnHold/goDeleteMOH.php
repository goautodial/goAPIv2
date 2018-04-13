<?php
 /**
 * @file 		goDeleteMOH.php
 * @brief 		API for Deleting Music On Hold
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
    
    ### Check campaign_id if its null or empty
	if($moh_id == null) { 
		$apiresults = array("result" => "Error: Set a value for MOH ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
		
		if (!checkIfTenant($groupId, $goDB)) {
        	//$ul = "AND moh_id='$moh_id'";
			$astDB->where('moh_id', $moh_id);
    	} else {
			//$ul = "AND moh_id='$moh_id' AND user_group='$groupId'";
			$astDB->where('moh_id', $moh_id);
			$astDB->where('user_group', $groupId);
		}

   		//$query = "SELECT moh_id FROM vicidial_music_on_hold WHERE remove='N' $ul ORDER BY moh_id LIMIT 1";
		$astDB->where('remove', 'N');
		$astDB->orderBy('moh_id', 'desc');
   		$rsltv = $astDB->getOne('vicidial_music_on_hold', 'moh_id');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			$dataMOHID = $rsltv['moh_id'];

			if(!$dataMOHID == null) {
				//$deleteQueryA = "DELETE FROM vicidial_music_on_hold WHERE moh_id IN ('$dataMOHID')";
				$astDB->where('moh_id', array($dataMOHID), 'in');
   				$astDB->delete('vicidial_music_on_hold');
				$deleteResultA = $astDB->getLastQuery();
				//$deleteQueryB = "DELETE FROM vicidial_music_on_hold_files WHERE moh_id IN ('$dataMOHID')";
				$astDB->where('moh_id', array($dataMOHID), 'in');
   				$astDB->delete('vicidial_music_on_hold_files');
				$deleteResultB = $astDB->getLastQuery();
				//echo $deleteQuery;
				
				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Music On-Hold: $dataMOHID", $log_group, $deleteQueryA);
				
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: MOH doesn't exist.");
			}
		} else {
			$apiresults = array("result" => "Error: MOH doesn't exist.");
		}
	}
?>
