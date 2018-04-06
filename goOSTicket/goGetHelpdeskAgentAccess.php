<?php
 /**
 * @file 		goGetHelpdeskAgentAccess.php
 * @brief 		API for Getting Helpdesk Agent Access - OS Ticket
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
    ### Check user_id if its null or empty
    if($userid == null && $userid == 0) { 
        $apiresults = array("result" => "Error: Set a value for User ID."); 
    } else {         
        $userid = $userid;
        $groupId = go_get_groupid($goUser, $astDB);

        if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
        } else { 
            $ul = "AND p.user_group='$groupId'";  
        }

        //$query = "SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid'";
        $ostDB->where('staff_id', $userid);
        $rsltv = $ostDB->get('ost_staff_dept_access', null, 'dept_id');
        //var_dump($rsltv);
        $countResult = $ostDB->getRowCount();
        
        if($countResult > 0) {
            $data = array();
            foreach ($rsltv as $fresults){
                array_push($data, ($fresults));
            }
            $apiresults = array("result" => "success", "data" => $data);
        } else {
            $apiresults = array("result" => "Error: No data to show.");
        }
    }
?>