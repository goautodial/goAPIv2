<?php
    #######################################################
    #### Name: goGetTicketInformation.php	       ####
    #### Description: API to get ticket details	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    //$userid = $_REQUEST['userid'];
    $ticketid = $_REQUEST['ticket_id'];
    
    if($ticketid == null && $ticketid == 0) { 
            $apiresults = array("result" => "Error: Set a value for Ticket ID"); 
    } else {
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
                $ul='';
        } else { 
                $ul = "AND p.user_group='$groupId'";  
        }
        
        $query = "SELECT ote.user_id, ote.source, poster, ote.title, body, ote.created, ot.number  FROM ost_thread_entry as ote, ost_ticket as ot WHERE id='$ticketid'";
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
