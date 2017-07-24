<?php
/*
#######################################################
#### Name: goGetLocationsList.php	               ####
#### Description: API to get all user group        ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
#### Written by: Chris Lomuntad                    ####
#### License: AGPLv2                               ####
#######################################################
*/
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 100; } else { $limit = $limit; }
 
    //$groupId = go_get_groupid($session_user);
    $groupId = $_REQUEST['group_id'];
    
	if(!checkIfTenant($groupId) && $groupId !== "ADMIN") {
		$goDB->where('user_group', $groupId);
	}

	$goDB->orderBy('name', 'desc');
	$rsltv = $goDB->get('locations', $limit, 'name,description,user_group,active');

	foreach ($rsltv as $row) {
		$dataLocation[] = $row['name'];
       	$dataDescription[] = $row['description'];
		$dataUserGroup[] = $row['user_group'];
		$dataActive[] = $row['active'];
	}
	$APIResult = array("result" => "success", "location" => $dataLocation, "description" => $dataDescription, "user_group" => $dataUserGroup, "active" => $dataActive);

?>
