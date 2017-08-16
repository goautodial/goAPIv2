<?php
/**********************************************
 * Name: goDeleteLocation.php                 *
 * Description: API to delete Location        *
 * Version: 0.9                               *
 * Copyright: GOAutoDial Ltd. (c) 2011-2015   *
 * Written by: Christopher P. Lomuntad        *
 * License: AGPLv2                            *
 *********************************************/
    
    // POST or GET Variables
    $location = $goDB->escape($_REQUEST['location']);
	
	$log_user = $goDB->escape($_REQUEST['log_user']);
	$log_group = $goDB->escape($_REQUEST['log_group']);
	$ip_address = $goDB->escape($_REQUEST['hostname']);
    
    
	if($location == null) {
		$err_msg = error_handle("40001");
		$APIResult = array("code" => "40001", "result" => $err_msg);
	} else {
		$groupId = go_get_groupid($goUser);
		
		$goDB->where('name', $location);
		if (checkIfTenant($groupId)) {
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
			$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $deleteQuery);
		}
	}//end
?>
