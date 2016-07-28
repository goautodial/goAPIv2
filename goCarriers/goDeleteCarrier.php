<?php
    #######################################################
    #### Name: goDeleteCarrier.php	               ####
    #### Description: API to delete specific carrier   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $carrier_id = $_REQUEST['carrier_id'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    
    ### Check campaign_id if its null or empty
	if($carrier_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Carrier ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE carrier_id='$carrier_id'";
    		} else { 
			$ul = "WHERE carrier_id='$carrier_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT carrier_id FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataCarrierID = $fresults['carrier_id'];
			}

			if(!$dataCarrierID == null) {
				$deleteQuery = "DELETE FROM vicidial_server_carriers WHERE carrier_id = '$carrier_id';";
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Carrier ID $dataCarrierID','DELETE FROM vicidial_server_carriers WHERE carrier_id = $carrier_id;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);



				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Carrier doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Carrier doesn't exist.");
		}
	}//end
?>
