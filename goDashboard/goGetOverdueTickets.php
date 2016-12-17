<?php
    ####################################################
    #### Name: goGetOverdueTickets.php              ####
    #### Type: API to get overdue tickets           ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $userid = $_REQUEST['userid'];
    $groupId = go_get_groupid($goUser);
    
    if($userid == null && $userid == 0) { 
            $apiresults = array("result" => "Error: Set a value for User ID"); 
    } else {    
    
        if (!checkIfTenant($groupId)) {
            $ul='';
        } else { 
            $stringv = go_getall_allowed_users($groupId);
            $stringv .= "'j'";
            $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
        }
    
        $query = "SELECT count(*) as overduetickets FROM ost_ticket WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='open') AND dept_id IN ((select dept_id from ost_staff where staff_id='$userid'),(SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid')) AND isoverdue=1;";

        $rsltv = mysqli_query($linkost,$query);
        $fresults = mysqli_fetch_assoc($rsltv);
        $apiresults = array_merge( array( "result" => "success" ), $fresults);
        
    }
?>
