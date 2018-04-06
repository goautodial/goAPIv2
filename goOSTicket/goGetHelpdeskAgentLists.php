<?php
 /**
 * @file 		goGetHelpdeskAgentLists.php
 * @brief 		API for Getting Helpdesk Agent Lists - OS Ticket
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

    $groupId = go_get_groupid($goUser, $astDB);

    if (!checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
        $ul = "AND p.user_group='$groupId'";  
    }

    $query = "SELECT staff_id, dept_id, role_id, username, firstname, lastname, isactive, id, name as dept_name FROM ost_staff, ost_department WHERE dept_id=id ORDER BY username DESC LIMIT 2000";

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

    function urlencode_array($array) {
        $out_array = array();
        foreach($array as $key => $value) {
            $out_array[rawurlencode($key)] = rawurlencode($value);
        }
        return $out_array;
    }
?>