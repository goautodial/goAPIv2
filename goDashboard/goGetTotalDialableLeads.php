<?php
    ######################################################
    #### Name: goGetTotalDialableLeads.php            ####
    #### Description: API to get total dialable leads ####
    #### Version: 0.9                                 ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014     ####
    #### Written by: Jeremiah Sebastian V. Samatra    ####
    #### License: AGPLv2                              ####
    ######################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $ul = " where campaign_id IN ($stringv)";
    }
   $query = "SELECT sum(dialable_leads) as getTotalDialableLeads FROM vicidial_campaign_stats $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );

?>
