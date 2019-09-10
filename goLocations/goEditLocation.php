<?php
 /**
 * @file 		goEditLocation.php
 * @brief 		API for Modifying Locations
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

    ### POST or GET Variables
    $location = $goDB->escape($_REQUEST['location']);
    $description = $goDB->escape($_REQUEST['description']);
    $user_group = $goDB->escape($_REQUEST['user_group']);
    $active = $goDB->escape($_REQUEST['active']);
		
########################
	if($location == null) {
		$APIResult = array("result" => "Error: Set a value for Location.");
	} else {
		if(preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $location)){
			$APIResult = array("result" => "Error: Special characters found in location");
		} else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $description)){
				$APIResult = array("result" => "Error: Special characters found in description");
			} else {
				if($active < 0 && $active != null || $active > 1 && $active != null) {
					$APIResult = array("result" => "Error: Active Value should be in between 0 and 1");
				} else {
					$groupId = go_get_groupid($goUser, $astDB);
		
					$goDB->where('name', $location);
					if (checkIfTenant($groupId, $goDB)) {
						$goDB->where('user_group', $groupId);
					}

					$goDB->orderBy('name', 'desc');
					$rsltv = $goDB->getOne('locations', 'name');
					$countResult = $goDB->getRowCount();
					if($countResult > 0) {
						$goDB->where('name', $location);
						$goDB->update('locations', array( 'description' => $description, 'user_group' => $user_group, 'active' => $active ));
	
						if($goDB->getRowCount() < 1){
							$APIResult = array("result" => "Error: Failed Update, Check your details");
						} else {
							$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Location: $location", $log_group, $goDB->getLastQuery());
		
							$APIResult = array("result" => "success");
						}
					} else {
						$APIResult = array("result" => "Error: Location doesn't exist. ");
					}
				}
			}
		}
	}
?>
