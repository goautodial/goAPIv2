<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");
    $goUser 						= mysqli_real_escape_string($link, $_REQUEST['goUser']);
    $ip_address 					= mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $campaign_id 					= mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $dial_status 					= mysqli_real_escape_string($link, $_REQUEST['dial_status']);
    $log_user                       = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group                      = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    if($campaign_id != null) {
        $updateQuery = "UPDATE vicidial_campaigns SET
                            dial_statuses = '$dial_status' 
                        WHERE campaign_id='$campaign_id'
                        LIMIT 1;";
        //echo $updateQuery;
        $updateResult = mysqli_query($link, $updateQuery);
        ### Admin logs
        //$SQLdate = date("Y-m-d H:i:s");
        //$queryLog = "INSERT INTO go_action_logs (
        //                user,
        //                ip_address,
        //                event_date,
        //                action,
        //                details,
        //                db_query
        //            ) values(
        //                '$goUser',
        //                '$ip_address',
        //                '$SQLdate','MODIFY',
        //                'MODIFY NEW CAMPAIGN $campaign_id',
        //                'UPDATE vicidial_campaigns SET dial_statuses=$new_status,
        //                WHERE campaign_id=$campaign_id LIMIT 1;'
        //            )";
        //$rsltvLog = mysqli_query($linkgo, $queryLog);
        $log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated Dial Statuses for Campaign ID: $campaign_id", $log_group, $updateQuery);
        
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Campaign doens't exist.");
    }
    
?>