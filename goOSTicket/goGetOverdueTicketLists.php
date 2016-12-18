<?php
    #######################################################
    #### Name: goGetOverdueTicketLists.php	       ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $userid = $_REQUEST['userid'];

    if($userid == null && $userid == 0) { 
            $apiresults = array("result" => "Error: Set a value for User ID"); 
    } else {
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
                $ul='';
        } else { 
                $ul = "AND p.user_group='$groupId'";  
        }

        $state= "open";
        
        //$query = "SELECT ticket_id, number, user_id, status_id, dept_id, sla_id, topic_id, staff_id, team_id, lock_id, flags, ip_address, source, source_extra, isoverdue, isanswered, duedate, est_duedate, reopened, reopened, closed, lastupdate, created, updated from ost_ticket ORDER by ticket_id DESC LIMIT $limit";
        $query = "SELECT number, ot.updated, ot.ticket_id, otc.subject, ou.name as customer, otp.priority from ost_ticket as ot, ost_ticket__cdata as otc, ost_user as ou, ost_ticket_priority as otp WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='$state') AND ot.ticket_id=otc.ticket_id AND ot.user_id=ou.id AND otc.priority=otp.priority_id AND dept_id IN ((select dept_id from ost_staff where staff_id='$userid'),(SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid')) AND isanswered=0 AND isoverdue=1 LIMIT 2000"; 

        $rsltv = mysqli_query($linkost,$query);
        //var_dump($rsltv);
        $countResult = mysqli_num_rows($rsltv);
        
        if($countResult > 0) {
            $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($data, urlencode_array($fresults));
            }
            $apiresults = array("result" => "success", "data" => $data);
        } else {
            $apiresults = array("result" => "Error: No data to show.");
        }                
    }
    
    function urlencode_array($array){
        $out_array = array();
        foreach($array as $key => $value){
            $out_array[rawurlencode($key)] = rawurlencode($value);
        }
        return $out_array;
    }
?>
