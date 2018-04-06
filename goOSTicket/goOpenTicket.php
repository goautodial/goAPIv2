<?php
 /**
 * @file 		goOpenTicket.php
 * @brief 		API for Open OS Ticket
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Noel Umandap  <noel@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    $goUser         = $ostDB->escape($_REQUEST['goUser']);
    $ip_address     = $ostDB->escape($_REQUEST['hostname']);
    $email          = $ostDB->escape($_REQUEST['email']);
    $fullname       = $ostDB->escape($_REQUEST['full_name']);
    $phoneNumber    = $ostDB->escape($_REQUEST['phone_number']);
    $company        = $ostDB->escape($_REQUEST['company']);
    $notes          = $ostDB->escape($_REQUEST['notes']);
    $topicID        = $ostDB->escape($_REQUEST['topic_id']);
    $title          = $ostDB->escape($_REQUEST['title']);
    $body           = $ostDB->escape($_REQUEST['body']);
    
    $date = date('Y-m-d H:i:s');
    
    //atatchments if necessary
    $attachment     = $_FILES['attachment']['tmp_name'];
    $resultsArray = array();
    //insert to ticket
    //$queryInsertTicket = "INSERT INTO ost_ticket(
    //                                        number,user_id,user_email_id,status_id,
    //                                        dept_id,sla_id,topic_id,staff_id,
    //                                        team_id,email_id,lock_id,flags,
    //                                        ip_address,source,isoverdue,isanswered,
    //                                        created,updated
    //                                    ) VALUES(
    //                                        '$ticketNO','$userID','$userEmailID','$statusID',
    //                                        '$deptID','$slaID','$topicID','$staffID',
    //                                        '$teamID','$emailID','$lockID','$flags',
    //                                        '$ip_address','$source','$isoverdue','$isanswered',
    //                                        '$date','$date'
    //                                    )";
    $insertData = array(
        'number' => $ticketNO,
        'user_id' => $userID,
        'user_email_id' => $userEmailID,
        'status_id' => $statusID,
        'dept_id' => $deptID,
        'sla_id' => $slaID,
        'topic_id' => $topicID,
        'staff_id' => $staffID,
        'team_id' => $teamID,
        'email_id' => $emailID,
        'lock_id' => $lockID,
        'flags' => $flags,
        'ip_address' => $ip_address,
        'source' => $source,
        'isoverdue' => $isoverdue,
        'isanswered' => $isanswered,
        'created' => $date,
        'updated' => $date
    );
    $resultInsertTicket = $ostDB->insert('ost_ticket', $insertData);
    if($resultInsertTicket){
        $ticketID = $ostDB->getInsertId();
        array_push($resultsArray, "ok");
    }else{
        $ticketID = '';
        array_push($resultsArray, "error");
    }
    
    //insert to ticket__cdata
    //$queryInsertTicketCData = "INSERT  INTO ost_ticket__cdata(
    //                                            ticket_id,subject,priority
    //                                        ) VALUES(
    //                                            '$ticketID','$title','$priority'
    //                                        )";
    $resultInsertTicketCData = $ostDB->insert('ost_ticket__cdata', array('ticket_id' => $ticketID, 'subject' => $title, 'priority' => $priority));
    if($ostDB->getInsertId() > 0){
        array_push($resultsArray, "ok");
    }else{
        array_push($resultsArray, "error");
    }
    
    //insert to thread
    //$queryInsertThread = "INSERT  INTO ost_thread(
    //                                        object_id,object_type,extra,lastresponse,lastmessage,created
    //                                    ) VALUES(
    //                                        '$ticketID','T','','','','$date'
    //                                    )";
    $insertData = array(
        'object_id' => $ticketID,
        'object_type' => 'T',
        'extra' => '',
        'lastresponse' => '',
        'lastmessage' => '',
        'created' => $date
    );
    $resultInsertThread = $ostDB->insert('ost_thread', $insertData);
    if($resultInsertThread){
        $threadID = $ostDB->getInsertId();
        array_push($resultsArray, "ok");
    }else{
        $threadID = '';
        array_push($resultsArray, "error");
    }
    
    //insert to thread entry
    //$queryInsertThreadEntry = "INSERT  INTO ost_thread_entry(
    //                                            thread_id,staff_id,user_id,type,
    //                                            flags,poster,title,body,
    //                                            format,ip_address,created,updated
    //                                        ) VALUES(
    //                                            '$threadID','$staffID','$userID','M',
    //                                            '$flags','$fullname','$title','$body',
    //                                            'html','$ip_address','$date',''
    //                                        )";
    $insertData = array(
        'thread_id' => $threadID,
        'staff_id' => $staffID,
        'user_id' => $userID,
        'type' => 'M',
        'flags' => $flags,
        'poster' => $fullname,
        'title' => $title,
        'body' => $body,
        'format' => 'html',
        'ip_address' => $ip_address,
        'created' => $date,
        'updated' => ''
    );
    $resultInsertThreadEntry = $ostDB->insert('ost_thread_entry', $insertData);
    if($resultInsertThreadEntry){
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