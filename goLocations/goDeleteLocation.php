<?php
 /**
 * @file 		goDeleteLocation.php
 * @brief 		API for Deleting Locations
 * @copyright 	Copyright (C) GOautodial Inc.
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

    // POST or GET Variables
    $location = $goDB->escape($_REQUEST['location']);
	    
	if($location == null) {
		$err_msg = error_handle("40001");
		$APIResult = array("code" => "40001", "result" => $err_msg);
	} else {
		$groupId = go_get_groupid($goUser, $astDB);
		
		$goDB->where('name', $location);
		if (checkIfTenant($groupId, $goDB)) {
			$goDB->where('user_group', $groupId);
		}
		
		//$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$goDB->orderBy('name', 'desc');
		$rsltv = $goDB->getOne('locations', 'name');
		$countResult = $goDB->getRowCount();
		
		if($countResult > 0) {
			$dataLocation = $rsltv['name'];
			
			if(!is_null($dataLocation)) {
				$goDB->where('name', $dataLocation);
				$rsltD = $goDB->delete('locations');
				$deleteQuery = $goDB->getLastQuery();
			} else {
				$err_msg = error_handle("10010");
				$APIResult = array("code" => "10010", "result" => $err_msg);
			}
		} else {
			$err_msg = error_handle("41004", "location. Does not exist");
			$APIResult = array("code" => "41004", "result" => $err_msg);
		}
		
		//$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$goDB->where('name', $location);
		$goDB->orderBy('name', 'desc');
		$rsltv = $goDB->getOne('locations', 'name');
		$countResult = $goDB->getRowCount();
	
		if($countResult > 0) {
			$err_msg = error_handle("41004", "location");
			$APIResult = array("code" => "41004", "result" => $err_msg);
		} else {
			$APIResult = array("result" => "success");
			$log_id = log_action($goDB, 'DELETE', $log_user, log_ip, "Deleted User Group: $dataUserGroup", $log_group, $deleteQuery);
		}
	}//end
?>
