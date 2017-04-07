<?php
    ####################################################
    #### Name: getAllAgents.php                     ####
    #### Type: API to get all Agents                ####
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

   $query = "SELECT full_name,pass FROM vicidial_users WHERE active='Y' AND (user_level<>'4' AND user_level < '7') $ul AND user NOT IN ('VDAD','VDCL', 'goAPI', 'goautodial') ORDER BY user";

    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
	
?>
