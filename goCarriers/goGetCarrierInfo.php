<?php
    #######################################################
    #### Name: goGetCarrierInfo.php	               ####
    #### Description: API to get specific Carrier      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $carrier_id = $_REQUEST['carrier_id'];
    
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

   		$query = "SELECT carrier_id,carrier_name,carrier_description,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataCarrierId[] = $fresults['carrier_id'];
                $dataCarrierName[] = $fresults['carrier_name'];
				$dataDesc[] = $fresults['carrier_description'];
                $dataServerIp[] = $fresults['server_ip'];
                $dataProtocol[] = $fresults['protocol'];
                $dataRegistrationString[] = $fresults['registration_string'];
                $dataActive[] = $fresults['active'];
                $dataUserGroup[] = $fresults['user_group'];
                $apiresults = array("result" => "success", "carrier_id" => $dataCarrierId, "carrier_name" => $dataCarrierName, "carrier_description" => $dataDesc, "server_ip" => $dataServerIp, "protocol" => $dataProtocol, "registration_string" => $dataRegistrationString, "active" => $dataActive, "user_group" => $dataUserGroup);

			}
		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}
?>
