<?php
    #######################################################
    #### Name: goGetDIDInfo.php		               ####
    #### Description: API to get specific DID	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
    $did_id = $_REQUEST['did_id'];
    
	if($did_id == null) { 
		$apiresults = array("result" => "Error: Set a value for DID ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE did_id='$did_id'";
    		} else { 
			$ul = "WHERE did_id='$did_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT did_id,did_pattern,did_description,did_active,did_route,record_call,filter_clean_cid_number from vicidial_inbound_dids $ul order by did_pattern LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);
		
		if($countResult > 0) {
			$fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
       
			$apiresults = array( "result" => "success", "data" => $fresults);

		} else {
			$apiresults = array("result" => "Error: DID doesn't exist.");
		}
	}
?>
