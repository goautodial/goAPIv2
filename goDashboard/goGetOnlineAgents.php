<?php
 ####################################################
 #### Name: goGetOnlineAgents.php                ####
 #### Type: API for dashboard php encode         ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
 #### Written by: Demian Lizandro Biscocho       ####
 #### License: AGPLv2                            ####
 ####################################################

 include "goFunctions.php";
 
// $groupId = go_get_groupid($goUser);

//    if (!checkIfTenant($groupId)) {
//        $ul = "";
//            } else {
//                    $stringv = go_getall_allowed_campaigns($goUser);
//                    $ul = " and campaign_id IN ('$stringv') ";
//    }
    
    
    $query = "CALL get_OnlineAgents()";
    $rsltv = mysqli_query($link,$query);
//    $fresults = mysqli_fetch_assoc($rsltv);

    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataAgentFullName[] = $fresults['agent_full_name'];
                $dataStatus[] = $fresults['status'];
                $dataCampaign[] = $fresults['campaign'];
                $dataLastStateChange[] = $fresults['last_state_change'];
                $dataLastCallTime[] = $fresults['last_call_time'];
                $dataLeadID[] = $fresults['lead_id'];
                
    $apiresults = array_merge( array( "result" => "success", "agent_full_name" => $dataAgentFullName, "status" => $dataStatus, "campaign" => $dataCampaign, "last_state_change" => $dataLastStateChange, "last_call_time" => $dataLastCallTime, "lead_id" => $dataLeadID ));  
}
?>
