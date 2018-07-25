<?php
 /**
 * @file 		goGetClusterStatus.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho  <demian@goautodial.com>
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

    include_once ("goAPI.php");
 
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	//$log_ip 										= $astDB->escape($_REQUEST['log_ip']);  
    
    ### ERROR CHECKING 
	if (!isset($log_user) || is_null($log_user)){
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {
		$query = "SELECT s.server_id, s.server_description, s.server_ip, s.active, s.sysload, s.channels_total, s.cpu_idle_percent, s.disk_usage, su.last_update as s_time,UNIX_TIMESTAMP(su.last_update) as u_time FROM servers s, server_updater su WHERE s.server_ip=su.server_ip LIMIT 100";

		$rsltv = $astDB->rawQuery($query);
		$countResult = $astDB->getRowCount();

		if($countResult > 0){
		
			$data = array();
			
			foreach ($rsltv as $fresults){
				array_push($data, $fresults);
			}
			
			$apiresults = array("result" => "success", "data" => $data);
		} 
	}

?>
