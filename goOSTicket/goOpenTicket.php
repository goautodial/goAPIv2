<?php
    #######################################################
    #### Name: goCreateTicket.php	                   ####
    #### Description: API to open/create ticket        ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2017      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $goUser         = $_REQUEST['goUser'];
    $ip_address     = $_REQUEST['hostname'];
    $email          = mysqli_real_escape_string($linkost, $_REQUEST['email']);
    $fullname       = mysqli_real_escape_string($linkost, $_REQUEST['full_name']);
    $phoneNumber    = mysqli_real_escape_string($linkost, $_REQUEST['phone_number']);
    $company        = mysqli_real_escape_string($linkost, $_REQUEST['company']);
    $notes          = mysqli_real_escape_string($linkost, $_REQUEST['notes']);
    $topicID        = mysqli_real_escape_string($linkost, $_REQUEST['topic_id']);
    $title          = mysqli_real_escape_string($linkost, $_REQUEST['title']);
    $body           = mysqli_real_escape_string($linkost, $_REQUEST['body']);
    
    $date = date('Y-m-d H:i:s');
    
    //atatchments if necessary
    $attachment     = $_FILES['attachment']['tmp_name'];
    $resultsArray = array();
    //insert to ticket
    $queryInsertTicket = "INSERT INTO ost_ticket(
                                            number,user_id,user_email_id,status_id,
                                            dept_id,sla_id,topic_id,staff_id,
                                            team_id,email_id,lock_id,flags,
                                            ip_address,source,isoverdue,isanswered,
                                            created,updated
                                        ) VALUES(
                                            '$ticketNO','$userID','$userEmailID','$statusID',
                                            '$deptID','$slaID','$topicID','$staffID',
                                            '$teamID','$emailID','$lockID','$flags',
                                            '$ip_address','$source','$isoverdue','$isanswered',
                                            '$date','$date'
                                        )";
    $resultInsertTicket = mysqli_query($linkost,$queryInsertTicket);
    if(mysqli_num_rows($resultInsertTicket) > 0){
        $ticketID = mysqli_insert_id($linkost);
        array_push($resultsArray, "ok");
    }else{
        $ticketID = '';
        array_push($resultsArray, "error");
    }
    
    //insert to ticket__cdata
    $queryInsertTicketCData = "INSERT  INTO ost_ticket__cdata(
                                                ticket_id,subject,priority
                                            ) VALUES(
                                                '$ticketID','$title','$priority'
                                            )";
    $resultInsertTicketCData = mysqli_query($linkost,$queryInsertTicketCData);
    if(mysqli_num_rows($resultInsertTicketCData) > 0){
        array_push($resultsArray, "ok");
    }else{
        array_push($resultsArray, "error");
    }
    
    //insert to thread
    $queryInsertThread = "INSERT  INTO ost_thread(
                                            object_id,object_type,extra,lastresponse,lastmessage,created
                                        ) VALUES(
                                            '$ticketID','T','','','','$date'
                                        )";
    $resultInsertThread = mysqli_query($linkost,$queryInsertThread);
    if(mysqli_num_rows($resultInsertThread) > 0){
        $threadID = mysqli_insert_id($linkost);
        array_push($resultsArray, "ok");
    }else{
        $threadID = '';
        array_push($resultsArray, "error");
    }
    
    //insert to thread entry
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
        array_push($resultsArray, "ok");
    }else{
        array_push($resultsArray, "error");
    }
    
    if(in_array("error", $resultsArray)){
        $apiresults = array("result" => "Error: Something went wrong.");
    }else{
        $apiresults = array("result" => "success");
    }
    
?>