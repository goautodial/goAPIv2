<?php
 /**
 * @file 		goGetLocationsList.php
 * @brief 		API for Getting Locations List
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

    $limit = $goDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 100; } else { $limit = $limit; }
 
    //$groupId = go_get_groupid($session_user);
    $groupId = $goDB->escape($_REQUEST['user_group']);
    
	if(!checkIfTenant($groupId, $goDB) && $groupId !== "ADMIN") {
		$goDB->where('user_group', $groupId);
	}

	$goDB->orderBy('name', 'desc');
	$rsltv = $goDB->get('locations', $limit, 'id,name,description,user_group,active');

	foreach ($rsltv as $row) {
		$dataLocationID[] = $row['id'];
		$dataLocation[] = $row['name'];
       	$dataDescription[] = $row['description'];
		$dataUserGroup[] = $row['user_group'];
		$dataActive[] = $row['active'];
	}
	$APIResult = array("result" => "success", "location_id" => $dataLocationID, "location" => $dataLocation, "description" => $dataDescription, "user_group" => $dataUserGroup, "active" => $dataActive);

?>
