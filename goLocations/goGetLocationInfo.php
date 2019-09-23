<?php
 /**
 * @file 		goGetLocationInfo.php
 * @brief 		API for Getting Location Info
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
    $location = $astDB->escape($_REQUEST['location']);
    
	if($location == null) { 
		$APIResult = array("code" => "41004", "result" => "Error: Set a value for Location."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
    
		$goDB->where('name', $location);
		if (checkIfTenant($groupId, $goDB)) {
			$goDB->where('user_group', $groupId);
		}

   		//$query = "SELECT user_group,group_name,forced_timeclock_login,shift_enforcement,allowed_campaigns FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$goDB->orderBy('name', 'desc');
		$rsltv = $goDB->getOne('locations', 'id,name,description,user_group,active,date_add,date_edit');
		$countResult = $goDB->getRowCount();
		$data = $rsltv;
		
		$log_id = log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the info of Location: $location", $log_group);

        $APIResult = array("result" => "success", "data" => $data);
	}
?>
