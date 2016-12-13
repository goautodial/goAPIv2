<?php
    #######################################################
    #### Name: goGetMyTicketLists.php	               ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    $userid = $_REQUEST['userid'];
    //var_dump($userid);
    
    if ($limit < 1) { 
        $limit = 2000; 
    } else { 
        $limit = $limit; 
    }
     
    $groupId = go_get_groupid($goUser);

    if (!checkIfTenant($groupId)) {
            $ul='';
    } else { 
            $ul = "AND p.user_group='$groupId'";  
    }

    //$query = "SELECT ticket_id, number, user_id, status_id, dept_id, sla_id, topic_id, staff_id, team_id, lock_id, flags, ip_address, source, source_extra, isoverdue, isanswered, duedate, est_duedate, reopened, reopened, closed, lastupdate, created, updated from ost_ticket ORDER by ticket_id DESC LIMIT $limit";
    $query = "SELECT ost_ticket.ticket_id as 'ot_ticket_id', number, ost_ticket.user_id as 'ot_user_id', status_id,  ost_ticket.staff_id as 'ot_staff_id', lastupdate, ost_ticket.created as 'ot_created', ost_ticket.updated as 'ot_updated', ost_staff.staff_id as 'os_staff_id', ost_ticket__cdata.ticket_id as 'otc_ticket_id', ost_ticket__cdata.priority as 'otc_priority', subject, firstname, lastname, name, ost_user.default_email_id as 'id' FROM ost_ticket, ost_staff, ost_ticket__cdata, ost_user  WHERE ost_ticket.staff_id=ost_staff.staff_id AND ost_ticket.ticket_id=ost_ticket__cdata.ticket_id and ost_user.default_email_id=ost_ticket.user_id and ost_ticket.staff_id='$userid' ORDER by number DESC LIMIT 2000";

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

    function urlencode_array($array){
        $out_array = array();
        foreach($array as $key => $value){
        $out_array[rawurlencode($key)] = rawurlencode($value);
        }
    return $out_array;
    }

?>
