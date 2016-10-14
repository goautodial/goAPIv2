<?php
    #######################################################
    #### Name: getCampaignInfo.php	               ####
    #### Description: API to get specific campaign     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
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

   		//$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
		$query = "SELECT * FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$queryGoCampaign = "SELECT campaign_type,custom_fields_launch,custom_fields_list_id FROM go_campaigns WHERE campaign_id='$campaign_id' LIMIT 1";
				$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
				while($typeresults = mysqli_fetch_array($rsltvGoCampaign, MYSQLI_ASSOC)){
					$campaign_type = $typeresults['campaign_type'];
					$custom_fields_launch = $typeresults['custom_fields_launch'];
					$custom_fields_list_id = $typeresults['custom_fields_list_id'];
				}
				$custom_fields_launch = (gettype($custom_fields_launch) != 'NULL') ? $custom_fields_launch : 'ONCALL';
				$custom_fields_list_id = (gettype($custom_fields_list_id) != 'NULL') ? $custom_fields_list_id : '';
				$apiresults = array("result" => "success", "data" => $fresults, "campaign_type" => $campaign_type, "custom_fields_launch" => $custom_fields_launch, 'custom_fields_list_id' => $custom_fields_list_id);
				//$apiresults = array("result" => "success", "data" => $fresults);
			}
		} else {
			$apiresults = array("result" => "Error: Campaign doesn't exist.", "COUNT:" => $countResult);
		}
	}//end
?>
