<?php
 /**
 * @file 		goPostReplyTicket.php
 * @brief 		API for Posting Reply on OS Ticket
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

    $goUser     = $ostDB->escape($_REQUEST['goUser']);
    $ip_address = $ostDB->escape($_REQUEST['hostname']);
    $ticketID   = $ostDB->escape($_REQUEST['ticket_id']);
    $threadID   = $ostDB->escape($_REQUEST['thread_id']);
    $userID     = $ostDB->escape($_REQUEST['user_id']);
    $fullname   = $ostDB->escape($_REQUEST['full_name']);
    $title      = $ostDB->escape($_REQUEST['title']);
    $body       = $ostDB->escape($_REQUEST['body']);
    $status     = $ostDB->escape($_REQUEST['status']);
    $date       = date('Y-m-d H:i:s');
    $resultsArray = array();
    
    //insert thread entry
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
        //update thread table
        //$queryUpdateThread = "UPDATE ost_thread SET
        //                    lastresponse = '$date',                  
        //                    lastmessage = '$date'
        //                WHERE object_id='$ticketID' AND id='$threadID' LIMIT 1;";
        $updateData = array(
            'lastresponse' => $date,
            'lastmessage' => $date
        );
        $ostDB->where('object_id', $ticketID);
        $ostDB->where('id', $threadID);
        $resultUpdateThread = $ostDB->update('ost_thread', $updateData, 1);
        if($ostDB->getRowCount() > 0){
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