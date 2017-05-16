<?php
    #######################################################
    #### Name: goDeleteTicket.php	                   ####
    #### Description: API to odelete ticket            ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2017      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $ticketid = mysqli_real_escape_string($linkost, $_REQUEST['ticket_id']);
    
    if(!empty($ticketid)){
        //delete from ticket table
        $queryDeleteTicket = "DELETE FROM ost_ticket WHERE ticket_id='$ticketid';";
        $resultDeleteTicket = mysqli_query($linkost,$queryDeleteTicket);
        
        //get thread id
        $queryGetThread = "SELECT * FROM ost_thread WHERE object_id='$ticketid';";
        $resultThread = mysqli_query($linkost,$queryGetThread);
        while($thread = mysqli_fetch_array($resultThread, MYSQLI_ASSOC)){
            $threadid= $thread['id'];
        }
        
        if(!empty($threadid)){
            //delete all thread from thread entry
            $queryDeleteThreadEntry = "DELETE FROM ost_thread_entry WHERE thread_id='$threadid';";
            $resultDeleteThreadEntry = mysqli_query($linkost,$queryDeleteThreadEntry);
            
            //delete from thread
            $queryDeleteThread= "DELETE FROM ost_thread WHERE id='$threadid';";
            $resultDeleteThread = mysqli_query($linkost,$queryDeleteThread);
        }
        
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Ticket id not found.");
    }
    
?>