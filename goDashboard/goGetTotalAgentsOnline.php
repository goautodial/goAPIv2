<?php
    ####################################################
    #### Name: goGetTotalAgentsOnline.php           ####
    #### Type: API to get total agents online       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul=' where user_level != 4';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = " where user IN ($stringv) and user_level != 4";
    }
    
    $query = "SELECT count(*) as getTotalAgentsOnline FROM vicidial_live_agents $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
