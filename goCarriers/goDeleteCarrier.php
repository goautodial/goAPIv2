<?php
    #######################################################
    #### Name: goDeleteCarrier.php	               ####
    #### Description: API to delete specific carrier   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $ip_address = $astDB->escape($_REQUEST['hostname']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
    
    ### Check campaign_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser, $astDB);
    
		if (!checkIfTenant($groupId, $goDB)) {
        	//$ul = "WHERE carrier_id='$carrier_id'";
    	} else { 
			//$ul = "WHERE carrier_id='$carrier_id' AND user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}

   		//$query = "SELECT carrier_id, server_ip FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 1;";
		$astDB->where('carrier_id', $carrier_id);
   		$rsltv = $astDB->getOne('vicidial_server_carriers', 'carrier_id, server_ip');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			$dataCarrierID = $rsltv['carrier_id'];
			$server_ip = $rsltv['server_ip'];

			if(!$dataCarrierID == null) {
				//$deleteQuery = "DELETE FROM vicidial_server_carriers WHERE carrier_id = '$carrier_id';";
				$astDB->where('carrier_id', $carrier_id);
   				$deleteResult = $astDB->delete('vicidial_server_carriers');
				//echo $deleteQuery;
				
				//$queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
				$astDB->where('generate_vicidial_conf', 'Y');
				$astDB->where('active_asterisk_server', 'Y');
				$astDB->where('server_ip', $server_ip);
				$resultVSC = $astDB->update('servers', array('rebuild_conf_files' => 'Y'));
        ### Admin logs
				//$SQLdate = date("Y-m-d H:i:s");
				//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Carrier ID $dataCarrierID','DELETE FROM vicidial_server_carriers WHERE carrier_id = $carrier_id;');";
				//$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Carrier ID: $dataCarrierID", $log_group, $deleteQuery);



				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Carrier doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}//end
?>
