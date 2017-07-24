<?php
/**********************************************
 * Name: goAddLocation.php                    *
 * Description: API to add new Location       *
 * Version: 0.9                               *
 * Copyright: GOAutoDial Ltd. (c) 2011-2015   *
 * Written by: Jeremiah Sebastian V. Samatra  *
 * License: AGPLv2                            *
 *********************************************/
    
    // POST or GET Variables
	$location = $goDB->escape($_REQUEST['location']);
	$description = $goDB->escape($_REQUEST['description']);
	$user_group = explode(",", $_REQUEST['user_group']);
	
	$ip_address = $goDB->escape($_REQUEST['hostname']);
	$log_user = $goDB->escape($_REQUEST['log_user']);
	$log_group = $goDB->escape($_REQUEST['log_group']);

    // Error checking
	if($location == null || $location == "") {
		$err_msg = error_handle("40001");
		$APIResult = array("code" => "40001","result" => $err_msg);
	} else {
        if(strlen($location) < 3 ) {
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
					$groupId = go_get_groupid($goUser);
		
					$goDB->where('name', $location);
					if (checkIfTenant($groupId)) {
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
						
						$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Location $location under $user_group User Group(s)", $log_group, $goDB->getLastQuery());
						
						if($countCheck > 0) {
							$APIResult = array("result" => "success", "location" => $location, "user_group" => $user_group);
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
