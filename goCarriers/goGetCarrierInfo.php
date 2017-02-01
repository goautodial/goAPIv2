<?php
    #######################################################
    #### Name: goGetCarrierInfo.php	 				   ####
    #### Description: API to get specific Carrier      ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2016	       ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $carrier_id = mysqli_real_escape_string($link, $_REQUEST['carrier_id']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    
    ### Check carrier_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser);

		if (!checkIfTenant($groupId)) {
				$ul = "WHERE carrier_id ='$carrier_id'";
			} else { 
			$ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";  
		}
	
			$query = "SELECT carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group, account_entry, dialplan_entry, registration_string, globals_string FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
			$rsltv = mysqli_query($link, $query);
			$countResult = mysqli_num_rows($rsltv);
			$fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
			
			if($countResult > 0) {
				$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the info of carrier id: $carrier_id", $log_group);
				
				$apiresults = array("result" => "success", "data" => $fresults);
			} else {
				$apiresults = array("result" => "Error: Carrier doesn't exist.");
			}
	}
?>
