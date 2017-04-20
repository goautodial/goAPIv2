<?php
    ####################################################
    #### Name: goGetTotalNumberOfAgents.php         ####
    #### Type: API to get all phones                ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Waren Ipac Briones             ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $ul = "AND user_group='$groupId'";
    }

    $query = "select count(user) as num_seats from vicidial_users where user_level < '4' and user NOT IN ('VDAD','VDCL', 'goAPI','goautodial') $ul";
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
