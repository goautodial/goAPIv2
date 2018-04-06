<?php
 /**
 * @file 		goEditTicket.php
 * @brief 		API for Modifying OS Ticket
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
    $threadID   = $ostDB->escape($_REQUEST['thread_id']);
    $title      = $ostDB->escape($_REQUEST['title']);
    $body       = $ostDB->escape($_REQUEST['body']);
    $date       = date('Y-m-d H:i:s');
    
    //update thread entry
    //$queryUpdateThreadEntry = "UPDATE vicidial_campaigns SET
    //                        title = '$title',
    //                        body = '$body',
    //                        updated = '$date'
    //                    WHERE thread_id='$threadID' LIMIT 1;";
    $updateData = array(
        'title' => $title,
        'body' => $body,
        'updated' => $date
    );
    $ostDB->where('thread_id', $threadID);
    $resultUpdateThreadEntry = $ostDB->update('vicidial_campaigns', $updateData, 1);
    
    if($ostDB->getRowCount() > 0){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Something went wrong.");
    }
?>