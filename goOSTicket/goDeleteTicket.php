<?php
 /**
 * @file 		goDeleteTicket.php
 * @brief 		API for Deleting OS Ticket
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

    $ticketid = $ostDB->escape($_REQUEST['ticket_id']);
    
    if(!empty($ticketid)) {
        //delete from ticket table
        //$queryDeleteTicket = "DELETE FROM ost_ticket WHERE ticket_id='$ticketid';";
        $ostDB->where('ticket_id', $ticketid);
        $resultDeleteTicket = $ostDB->delete('ost_ticket');
        
        //get thread id
        //$queryGetThread = "SELECT * FROM ost_thread WHERE object_id='$ticketid';";
        $ostDB->where('object_id', $ticketid);
        $resultThread = $ostDB->get('ost_thread');
        foreach ($resultThread as $thread) {
            $threadid = $thread['id'];
        }
        
        if(!empty($threadid)) {
            //delete all thread from thread entry
            //$queryDeleteThreadEntry = "DELETE FROM ost_thread_entry WHERE thread_id='$threadid';";
            $ostDB->where('thread_id', $threadid);
            $resultDeleteThreadEntry = $ostDB->delete('ost_thread_entry');
            
            //delete from thread
            //$queryDeleteThread= "DELETE FROM ost_thread WHERE id='$threadid';";
            $ostDB->where('id', $threadid);
            $resultDeleteThread = $ostDB->delete('ost_thread');
        }
        
        $apiresults = array("result" => "success");
    } else {
        $apiresults = array("result" => "Error: Ticket id not found.");
    }
?>