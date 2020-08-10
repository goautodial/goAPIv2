<?php
   ####################################################
   #### Name: goUpdateCampaignGoogleSheet.php      ####
   #### Description: API to edit specific campaign ####
   ####              that has google sheet enabled.####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Christopher Lomuntad           ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");
    $goUser 						= mysqli_real_escape_string($link, $_REQUEST['goUser']);
    $ip_address 					= mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $campaign_id 					= mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $google_sheet_ids 				= mysqli_real_escape_string($link, $_REQUEST['google_sheet_ids']);
    $log_user                       = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group                      = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    if($campaign_id != null) {
        $updateQuery = "UPDATE go_campaigns SET
                            google_sheet_ids = '$google_sheet_ids' 
                        WHERE campaign_id='$campaign_id'
                        LIMIT 1;";
        //echo $updateQuery;
        $updateResult = mysqli_query($linkgo, $updateQuery);
        
        $log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated Google Sheets for Campaign ID: $campaign_id", $log_group, $updateQuery);
        
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Campaign doens't exist.");
    }
    
?>
