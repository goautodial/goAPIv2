<?php
 /**
 * @file 		goGetCannedResponseLists.php
 * @brief 		API for Getting Canned Response OS Ticket
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

    //$query = "SELECT canned_id, dept_id, isenabled, title, updated from ost_canned_response ORDER by updated DESC LIMIT 2000";
    $query = "SELECT isenabled, title, ost_canned_response.updated, name FROM ost_canned_response LEFT OUTER JOIN ost_department ON ost_canned_response.dept_id=ost_department.id";
    $rsltv = $ostDB->rawQuery($query);
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
            $out_array[urlencode($key)] = urlencode($value);
        }
        return $out_array;
    }
?>