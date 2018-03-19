<?php
    #######################################################
    #### Name: goGetCarrierInfo.php	 				   ####
    #### Description: API to get specific Carrier      ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2016	       ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
    
    ### Check carrier_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser, $astDB);

		if (!checkIfTenant($groupId, $goDB)) {
			//$ul = "WHERE carrier_id ='$carrier_id'";
		} else { 
			//$ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}
	
		//$query = "SELECT carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
		$astDB->where('carrier_id', $carrier_id);
		$astDB->orderBy('carrier_id', 'desc');
		$rsltv = $astDB->get('vicidial_server_carriers', 3, 'carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string');
		$countResult = $astDB->getRowCount();
		
		if($countResult > 0) {
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the info of carrier id: $carrier_id", $log_group);
			
			$apiresults = array("result" => "success", "data" => $rsltv);
		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}
?>
