<?php
    ####################################################
    #### Name: goGetOpenTickets.php                 ####
    #### Type: API to get total sales               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $userid = $_REQUEST['userid'];
    $groupId = go_get_groupid($goUser, $astDB);
    
    if($userid == null && $userid == 0) { 
        $apiresults = array("result" => "Error: Set a value for User ID"); 
    } else {     
    
        if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
        } else { 
            $stringv = go_getall_allowed_users($groupId, $astDB);
            $stringv .= "'j'";
            $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
        }
            
        $state = "open";
        $query = "SELECT count(*) as opentickets FROM ost_ticket WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='$state') AND dept_id IN ((select dept_id from ost_staff where staff_id='$userid'),(SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid')) AND isanswered=0";

        $fresults = $ostDB->rawQuery($query);
        //$fresults = mysqli_fetch_assoc($rsltv);
        $apiresults = array_merge( array( "result" => "success" ), $fresults);
    
    }
?>
