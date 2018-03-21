<?php
    ######################################################
    #### Name: goGetTotalDialableLeads.php            ####
    #### Description: API to get total dialable leads ####
    #### Version: 0.9                                 ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014     ####
    #### Written by: Jeremiah Sebastian V. Samatra    ####
    #### License: AGPLv2                              ####
    ######################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul = " where campaign_id IN ($stringv)";
    }
    $query = "SELECT sum(dialable_leads) as getTotalDialableLeads FROM vicidial_campaign_stats $ul"; 
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );

?>
