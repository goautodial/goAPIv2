<?php
 /**
 * @file 		goGetUnassignedAgents.php
 * @brief 		API for Getting Unassigned Agents - Locations
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

if (isset($_GET['location'])) { $location = $astDB->escape($_GET['location']); }
    else if (isset($_POST['location'])) { $location = $astDB->escape($_POST['location']); }
if (isset($_GET['agent'])) { $agent = $astDB->escape($_GET['agent']); }
    else if (isset($_POST['agent'])) { $agent = $astDB->escape($_POST['agent']); }
if (isset($_GET['active'])) { $active = $astDB->escape($_GET['active']); }
    else if (isset($_POST['active'])) { $active = $astDB->escape($_POST['active']); }

if (!is_null($location) && $location !== '') {
	$goDB->where('locations.name', $location);
}
if (!is_null($agent) && $agent !== '') {
	$goDB->where('users.name', $agent);
}
if (!is_null($active) && $active !== '') {
	$goDB->where("vu.active", $active);
}
$goDB->where('users.user_group', 'AGENTS');
$goDB->join('locations', 'users.location_id=locations.id', 'LEFT');
$goDB->join("`$VARDB_database`.vicidial_users AS vu", 'vu.user_id=users.userid', 'LEFT');
$rsltv = $goDB->get('users', null, 'users.userid,users.name,users.fullname,locations.name AS location_name, role, locations.id AS location_id');

$dataUserID = [];
$dataName = [];
$dataFullName = [];
$dataLocation = [];
$dataRole = [];
foreach ($rsltv as $row) {
	$dataUserID[] = $row['userid'];
	$dataLocationID[] = $row['location_id'];
	$dataName[] = $row['name'];
	$dataFullName[] = $row['fullname'];
	$dataLocation[] = $row['location_name'];
	$dataRole[] = $row['role'];
}
$APIResult = array("result" => "success", "user_id" => $dataUserID, "location_id" => $dataLocationID, "user" => $dataName, "full_name" => $dataFullName, "location_name" => $dataLocation, "role" => $dataRole);

?>
