<?php
    #######################################################
    #### Name: goCreateTicket.php	                   ####
    #### Description: API to post reply ticket         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2017      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $goUser     = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    $ticketID   = mysqli_real_escape_string($linkost, $_REQUEST['ticket_id']);
    $threadID   = mysqli_real_escape_string($linkost, $_REQUEST['thread_id']);
    $userID     = mysqli_real_escape_string($linkost, $_REQUEST['user_id']);
    $fullname   = mysqli_real_escape_string($linkost, $_REQUEST['full_name']);
    $title      = mysqli_real_escape_string($linkost, $_REQUEST['title']);
    $body       = mysqli_real_escape_string($linkost, $_REQUEST['body']);
    $status     = mysqli_real_escape_string($linkost, $_REQUEST['status']);
    $date       = date('Y-m-d H:i:s');
    $resultsArray = array();
    
    //insert thread entry
    $queryInsertThreadEntry = "INSERT  INTO ost_thread_entry(
                                                thread_id,staff_id,user_id,type,
                                                flags,poster,title,body,
                                                format,ip_address,created,updated
                                            ) VALUES(
                                                '$threadID','$staffID','$userID','M',
                                                '$flags','$fullname','$title','$body',
                                                'html','$ip_address','$date',''
                                            )";
    $resultInsertThreadEntry = mysqli_query($linkost,$queryInsertThreadEntry);
    if(mysqli_num_rows($resultInsertThreadEntry) > 0){
        //update thread table
        $queryUpdateThread = "UPDATE ost_thread SET
                            lastresponse = '$date',                  
                            lastmessage = '$date'
                        WHERE object_id='$ticketID' AND id='$threadID' LIMIT 1;";
        $resultUpdateThread = mysqli_query($linkost,$queryUpdateThread);
        if(mysqli_num_rows($resultUpdateThread) > 0){
            array_push($resultsArray, "ok");
        }else{
            array_push($resultsArray, "error");
        }                
    }else{
        array_push($resultsArray, "error");
    }
    
    if(in_array("error", $resultsArray)){
        $apiresults = array("result" => "Error: Something went wrong.");
    }else{
        $apiresults = array("result" => "success");
    }
    
        
?>