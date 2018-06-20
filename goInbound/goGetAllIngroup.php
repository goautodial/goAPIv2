<?php
 /**
 * @file        goGetAllIngroups.php
 * @brief       API to get all DID Details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian V. Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

    include_once ("goAPI.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 1000; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser, $astDB);
    
    if (checkIfTenant($groupId)) {
        $astDB->where("user_group", $user_group);
        //$ul = "WHERE user_group='$user_group'";
    }

    $cols = Array("group_id", "group_name", "queue_priority", "active", "call_time_id");
    $selectQuery = $astDB->get("vicidial_inbound_groups", $limit, $cols);
    //$query = "SELECT  FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT $limit;";
    
	foreach($selectQuery as $fresults){
    	$dataGroupId[] =  $fresults['group_id'];
    	$dataGroupName[] =  $fresults['group_name'];
    	$dataQueuePriority[] =  $fresults['queue_priority'];
    	$dataActive[] =  $fresults['active'];
    	$dataCallTimeId[] =  $fresults['call_time_id'];
	}
    
    $apiresults = array( "result" => "success", "group_id" => $dataGroupId, "group_name" => $dataGroupName, "queue_priority" => $dataQueuePriority, "active" => $dataActive, "call_time_id" => $dataCallTimeId);
?>
