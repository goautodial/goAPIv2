<?php
 /**
 * @file 		goAddLocation.php
 * @brief 		API for Locations
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	$description = $goDB->escape($_REQUEST['description']);
	$user_group = explode(",", $goDB->escape($_REQUEST['user_group']));

    // Error checking
	if($location == null || $location == "") {
		$err_msg = error_handle("40001");
		$APIResult = array("code" => "40001","result" => $err_msg);
	} else {
        if(strlen($location) < 2 ) {
            $err_msg = error_handle("41006", "location");
			$APIResult = array("code" => "41006","result" => $err_msg);
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $location)) {
				$err_msg = error_handle("41004", "location");
				$APIResult = array("code" => "41004","result" => $err_msg);
			} else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $description) || $description == null){
					$err_msg = error_handle("41004", "description");
					$APIResult = array("code" => "41004","result" => $err_msg);
				} else {
					$groupId = go_get_groupid($goUser, $astDB);
		
					$goDB->where('name', $location);
					if (checkIfTenant($groupId, $goDB)) {
						if (is_array($user_group)) {
							$goDB->where('user_group', $user_group, 'in');
						} else {
							$goDB->where('user_group', $user_group);
						}
					}
		
					//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
					$goDB->orderBy('name', 'desc');
					$rsltv = $goDB->getOne('locations', 'name,description,user_group,active');
					$countResult = $goDB->getRowCount();
					
					if($countResult > 0) {
						$err_msg = error_handle("41004", "location. Already exists");
						$APIResult = array("code" => "41004","result" => $err_msg);
					} else {
						$user_group = implode(",", $user_group);
						$insertData = array(
							'name' => $location,
							'description' => $description,
							'user_group' => $user_group,
							'active' => 1,
							'date_add' => date("Y-m-d H:i:s")
						);
						$goDB->insert('locations', $insertData);
						$countCheck = $goDB->getInsertId();
						
						$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New Location $location under $user_group User Group(s)", $log_group, $goDB->getLastQuery());
						
						//$get_location_id = mysqli_query($linkgo, "SELECT id FROM locations WHERE name = '$location';");
						$goDB->where('name', $location);
						$fetch_id = $goDB->getOne('locations', 'id');
						$location_id = $fetch_id['id'];

						if($countCheck > 0) {
							$APIResult = array("result" => "success", "location_id" => $location_id , "location" => $location, "user_group" => $user_group);
						} else {
							$err_msg = error_handle("10010");
							$APIResult = array("code" => "10010","result" => $err_msg);
						}
					}
				}
			}
		}
	}
?>