<?php
/**********************************************
 * Name: goGetLocationInfo.php                *
 * Description: API to get Location Info      *
 * Version: 0.9                               *
 * Copyright: GOAutoDial Ltd. (c) 2011-2015   *
 * Written by: Christopher P. Lomuntad        *
 * License: AGPLv2                            *
 *********************************************/
	
    ### POST or GET Variables
    $location = $_REQUEST['location'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    
	if($location == null) { 
		$APIResult = array("code" => "41004", "result" => "Error: Set a value for Location."); 
	} else {
    	$groupId = go_get_groupid($goUser);
    
		$goDB->where('name', $location);
		if (checkIfTenant($groupId)) {
			$goDB->where('user_group', $groupId);
		}

   		//$query = "SELECT user_group,group_name,forced_timeclock_login,shift_enforcement,allowed_campaigns FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$goDB->orderBy('name', 'desc');
		$rsltv = $goDB->getOne('locations', 'id,name,description,user_group,active,date_add,date_edit');
		$countResult = $goDB->getRowCount();
		$data = $rsltv;
		
		$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the info of Location: $location", $log_group);

        $APIResult = array("result" => "success", "data" => $data);
	}
?>
