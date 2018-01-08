<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    $goUser 						= mysqli_real_escape_string($link, $_REQUEST['goUser']);
    $ip_address 					= mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $campaign_id 					= mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $dial_status 					= mysqli_real_escape_string($link, $_REQUEST['dial_status']);
    $log_user                       = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group                      = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    if($campaign_id != null) {
        $astDB->where('campaign_id', $campaign_id);
        $updateQuery = $astDB->update('vicidial_campaigns', array('dial_statuses' => $dial_status));

        $log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated Dial Statuses for Campaign ID: $campaign_id", $log_group, $updateQuery);
        
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Campaign doens't exist.");
    }
    
?>