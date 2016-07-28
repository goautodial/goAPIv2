<?php
    #######################################################
    #### Name: getUserGroupInfo.php	               ####
    #### Description: API to get specific User Group   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "../goFunctions.php";
    
    ### POST or GET Variables
    $campaign_id = $_REQUEST['campaign_id'];
    
    ### Check campaign_id if its null or empty
	if($campaign_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE campaign_id='$campaign_id'";
    		} else { 
			$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
   		$rsltv = mysql_query($query, $link);
		$countResult = mysql_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysql_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataCampID[] = $fresults['campaign_id'];
	       			$dataCampName[] = $fresults['campaign_name'];
				$dataDialMethod[] = $fresults['dial_method'];
				$dataActive[] = $fresults['active'];
   				$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
			}
		} else {
			$apiresults = array("result" => "Error: Campaign doesn't exist.");
		}
	}//end
?>
