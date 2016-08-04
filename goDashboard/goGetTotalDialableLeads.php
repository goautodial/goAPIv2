<?php
    ######################################################
    #### Name: goGetTotalDialableLeads.php            ####
    #### Description: API to get total dialable leads ####
    #### Version: 0.9                                 ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014     ####
    #### Written by: Jeremiah Sebastian V. Samatra    ####
    #### License: AGPLv2                              ####
    ######################################################
    
    include "goFunctions.php";
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = " where campaign_id IN ($stringv) and user_level != 4";
    }
   $query = "SELECT sum(dialable_leads) as getTotalDialableLeads FROM vicidial_campaign_stats $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );

?>
