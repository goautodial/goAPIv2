<?php
 ####################################################
 #### Name: goGetHopperLeadsWarning.php          ####
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
    
    
    $query = "CALL get_HopperLeadsWarning()";
    $rsltv = mysqli_query($link,$query);
//    $fresults = mysqli_fetch_assoc($rsltv);

    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataMyCnt[] = $fresults['mycnt'];
                $dataCampaignID[] = $fresults['campaign_id'];
                $dataCampaignName[] = $fresults['campaign_name'];

    $apiresults = array_merge( array( "result" => "success", "mycnt" => $dataMyCnt, "campaign_id" => $dataCampaignID, "campaign_name" => $dataCampaignName ));  
}
?>
