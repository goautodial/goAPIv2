<?php
/**********************************************
 * Name: goEditLocation.php                   *
 * Description: API to edit Location Info     *
 * Version: 0.9                               *
 * Copyright: GOAutoDial Ltd. (c) 2011-2015   *
 * Written by: Christopher P. Lomuntad        *
 * License: AGPLv2                            *
 *********************************************/
 
    ### POST or GET Variables
    $ip_address = $goDB->escape($_REQUEST['hostname']);
    $location = $goDB->escape($_REQUEST['location']);
    $description = $goDB->escape($_REQUEST['description']);
    $user_group = $goDB->escape($_REQUEST['user_group']);
    $active = $goDB->escape($_REQUEST['active']);
	
	$log_user = $goDB->escape($_REQUEST['log_user']);
	$log_group = $goDB->escape($_REQUEST['log_group']);
	
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
					$groupId = go_get_groupid($goUser);
		
					$goDB->where('name', $location);
					if (checkIfTenant($groupId)) {
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
							$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Location: $location", $log_group, $goDB->getLastQuery());
		
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
