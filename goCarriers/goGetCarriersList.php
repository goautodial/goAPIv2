<?php
    #######################################################
    #### Name: goGetCarriersList.php	               ####
    #### Description: API to get all carriers          ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "WHERE user_group='$groupId'";  
	}

   	$query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers ORDER BY carrier_id LIMIT $limit;";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCarrierId[] = $fresults['carrier_id'];
       		$dataCarrierName[] = $fresults['carrier_name'];
		$dataServerIp[] = $fresults['server_ip'];
		$dataProtocol[] = $fresults['protocol'];
		$dataRegistrationString[] = $fresults['registration_string'];
		$dataActive[] = $fresults['active'];
		$dataUserGroup[] = $fresults['user_group'];
   		$apiresults = array("result" => "success", "carrier_id" => $dataCarrierId, "carrier_name" => $dataCarrierName, "server_ip" => $dataServerIp, "protocol" => $dataProtocol, "registration_string" => $dataRegistrationString, "active" => $dataActive, "user_group" => $dataUserGroup);
	}

?>
