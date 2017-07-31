<?php
/*
#######################################################
#### Name: goGetUnassignedAgents.php	           ####
#### Description: API to get all unassigned agents ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
#### Written by: Chris Lomuntad                    ####
#### License: AGPLv2                               ####
#######################################################
*/

if (isset($_GET['location'])) { $location = $astDB->escape($_GET['location']); }
    else if (isset($_POST['location'])) { $location = $astDB->escape($_POST['location']); }
if (isset($_GET['agent'])) { $agent = $astDB->escape($_GET['agent']); }
    else if (isset($_POST['agent'])) { $agent = $astDB->escape($_POST['agent']); }
if (isset($_GET['active'])) { $active = $astDB->escape($_GET['active']); }
    else if (isset($_POST['active'])) { $active = $astDB->escape($_POST['active']); }

$goDB->where('users.user_group', 'AGENTS');
$goDB->join('locations', 'users.location_id=locations.id', 'LEFT');
$rsltv = $goDB->get('users', null, 'users.userid,users.name,users.fullname,locations.name AS location_name, role');

$dataUserID = [];
$dataName = [];
$dataFullName = [];
$dataLocation = [];
$dataRole = [];
foreach ($rsltv as $row) {
	$dataUserID[] = $row['userid'];
	$dataName[] = $row['name'];
	$dataFullName[] = $row['fullname'];
	$dataLocation[] = $row['location_name'];
	$dataRole[] = $row['role'];
}
$APIResult = array("result" => "success", "user_id" => $dataUserID, "user" => $dataName, "full_name" => $dataFullName, "location_name" => $dataLocation, "role" => $dataRole);

?>
