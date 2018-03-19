<?php
    #######################################################
    #### Name: goGetCarriersList.php	               ####
    #### Description: API to get all carriers          ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    
    $limit = $astDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    $groupId = go_get_groupid($goUser, $astDB);
    
	if (!checkIfTenant($groupId, $goDB)) {
        //$ul='';
    } else { 
		//$ul = "WHERE user_group='$groupId'";
		$astDB->where('user_group', $groupId);
	}

   	//$query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group,dialplan_entry FROM vicidial_server_carriers ORDER BY carrier_id LIMIT $limit;";
	$astDB->orderBy('carrier_id', 'desc');
   	$rsltv = $astDB->get('vicidial_server_carriers', $limit, 'carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group,dialplan_entry');

	foreach ($rsltv as $fresults){
		$dataCarrierId[] = $fresults['carrier_id'];
       	$dataCarrierName[] = $fresults['carrier_name'];
		$dataServerIp[] = $fresults['server_ip'];
		$dataProtocol[] = $fresults['protocol'];
		$dataRegistrationString[] = $fresults['registration_string'];
		$dataActive[] = $fresults['active'];
		$dataUserGroup[] = $fresults['user_group'];
		$dataDialPlanEntry[] = $fresults['dialplan_entry'];
   		$apiresults = array("result" => "success", "carrier_id" => $dataCarrierId, "carrier_name" => $dataCarrierName, "server_ip" => $dataServerIp, "protocol" => $dataProtocol, "registration_string" => $dataRegistrationString, "active" => $dataActive, "user_group" => $dataUserGroup, "dialplan_entry" => $dataDialPlanEntry);
	}

?>
