<?php
 /**
 * @file 		goGetTicketInformation.php
 * @brief 		API for Getting OS Ticket Information
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

    //$userid = $_REQUEST['userid'];
    $ticketid = $ostDB->escape($_REQUEST['ticket_id']);
    
    if($ticketid == null && $ticketid == 0) { 
        $apiresults = array("result" => "Error: Set a value for Ticket ID"); 
    } else {
        $groupId = go_get_groupid($goUser, $astDB);

        if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
        } else { 
            $ul = "AND p.user_group='$groupId'";  
        }
        
        $query = "SELECT ote.user_id, ote.source, poster, ote.title, body, ote.created, ot.number  FROM ost_thread_entry as ote, ost_ticket as ot WHERE id='$ticketid'";
        $rsltv = $ostDB->rawQuery($query);
        //var_dump($rsltv);
        $countResult = $ostDB->getRowCount();
        
        if($countResult > 0) {
            $data = array();
            foreach ($rsltv as $fresults){
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