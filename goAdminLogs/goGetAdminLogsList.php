<?php
/**
 * @file 		goGetAdminLogsList.php
 * @brief 		API to view admin logs
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Chris Lomuntad
 * @author     	Alexander Jim Abenoja
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
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
	if (isset($_REQUEST['limit'])) {
			$limit = $astDB->escape($_REQUEST['limit']);
	} else { $limit = 100; }
    
    if(!isset($session_user) || is_null($session_user)){
    	$apiresults = array("result" => "Error: Missing Required Parameters.");
    }elseif(is_null($log_group)) { 
		$apiresults = array("result" => "Error: Set a value for User Group."); 
	} else {
		if (!checkIfTenant($log_group, $goDB)) {
			if($log_group !== "ADMIN")
			$goDB->where("user_group", $log_group);
		} else {
			$goDB->where('user_group', $log_group);
		}
	}
	

    $cols = array("user", "ip_address", "event_date", "action", "details", "db_query");
	$goDB->orderBy("event_date", "desc");
	$adminLogs = $goDB->get("go_action_logs", $limit, $cols);

	if (!empty($adminLogs)) {
		$apiresults = array("result" => "success", "data" => $adminLogs);
	} else {
		$apiresults = array("result" => "Error: Empty");
	}
	
?>
