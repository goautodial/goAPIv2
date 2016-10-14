<?php
    #######################################################
    #### Name: goDeleteDID.php		               ####
    #### Description: API to delete specific DID       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $did_id = $_REQUEST['did_id'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
    
	if($did_id == null) { 
		$apiresults = array("result" => "Error: Set a value for DID ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE did_id='$did_id'";
    		} else { 
			$ul = "WHERE did_id='$did_id' AND user_group='$groupId'";  
		}

		$query = "SELECT did_id,did_pattern from vicidial_inbound_dids $ul order by did_pattern LIMIT 1";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataDIDID = $fresults['did_id'];
			}

			if(!$dataDIDID == null) {
				$deleteQuery = "DELETE from vicidial_inbound_dids where did_id='$dataDIDID' limit 1;"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted DID ID $dataDIDID','DELETE from vicidial_inbound_dids where did_id=$dataDIDID limit 1;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

				//echo $deleteQuery;
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: DID doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: DID doesn't exist.");
		}
	}
?>
