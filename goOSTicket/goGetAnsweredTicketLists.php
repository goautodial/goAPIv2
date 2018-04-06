<?php
 /**
 * @file 		goGetAnsweredTicketLists.php
 * @brief 		API for Getting Answered OS Ticket
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Demian Lizandro Biscocho  <demian@goautodial.com>
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

    $userid = $ostDB->escape($_REQUEST['userid']);

    if($userid == null && $userid == 0) { 
        $apiresults = array("result" => "Error: Set a value for User ID"); 
    } else {
        $groupId = go_get_groupid($goUser, $astDB);

        if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
        } else { 
            $ul = "AND p.user_group='$groupId'";  
        }

        $state= "open";
        
        //$query = "SELECT ticket_id, number, user_id, status_id, dept_id, sla_id, topic_id, staff_id, team_id, lock_id, flags, ip_address, source, source_extra, isoverdue, isanswered, duedate, est_duedate, reopened, reopened, closed, lastupdate, created, updated from ost_ticket ORDER by ticket_id DESC LIMIT $limit";
        $query = "SELECT number, ot.updated, ot.ticket_id, otc.subject, ou.name as customer, otp.priority from ost_ticket as ot, ost_ticket__cdata as otc, ost_user as ou, ost_ticket_priority as otp WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='$state') AND ot.ticket_id=otc.ticket_id AND ot.user_id=ou.id AND otc.priority=otp.priority_id AND dept_id IN ((select dept_id from ost_staff where staff_id='$userid'),(SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid')) AND isanswered=1 LIMIT 2000"; 

        $rsltv = $ostDB->rawQuery($query);
        //var_dump($rsltv);
        $countResult = $ostDB->getRowCount();
        
        if($countResult > 0) {
            $data = array();
            foreach ($rsltv as $fresults) {
                array_push($data, urlencode_array($fresults));
            }
            $apiresults = array("result" => "success", "data" => $data);
        } else {
            $apiresults = array("result" => "Error: No data to show.");
        }                
    }
    
    function urlencode_array($array) {
        $out_array = array();
        foreach($array as $key => $value) {
            $out_array[rawurlencode($key)] = rawurlencode($value);
        }
        return $out_array;
    }
?>