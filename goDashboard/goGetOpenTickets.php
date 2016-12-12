<?php
    ####################################################
    #### Name: goGetOpenTickets.php                 ####
    #### Type: API to get total sales               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
    }

    //$NOW = date('Y-m-d');    
    //$YESTERDAY = date('Y-m-d',strtotime('-1 days'));
    
    $status_id = "3"; //for open tickets
   
    $query = "select count(*) as opentickets from ost_ticket where status_id='$status_id'";

    $rsltv = mysqli_query($linkost,$query);
    $fresults = mysqli_fetch_assoc($rsltv);

    $apiresults = array_merge( array( "result" => "success" ), $fresults);
?>
