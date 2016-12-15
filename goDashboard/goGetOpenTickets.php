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
    
    $deptid = $_REQUEST['deptid'];
    $groupId = go_get_groupid($goUser);
    
    if($deptid == null && $deptid == 0) { 
            $apiresults = array("result" => "Error: Set a value for Departmet ID"); 
    } else {     
    
        if (!checkIfTenant($groupId)) {
            $ul='';
        } else { 
            $stringv = go_getall_allowed_users($groupId);
            $stringv .= "'j'";
            $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
        }
            
        $query = "SELECT count(*) as opentickets FROM ost_ticket WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='open') AND dept_id IN ($deptid) AND isanswered=0";

        $rsltv = mysqli_query($linkost,$query);
        $fresults = mysqli_fetch_assoc($rsltv);
        $apiresults = array_merge( array( "result" => "success" ), $fresults);
    
    }
?>
