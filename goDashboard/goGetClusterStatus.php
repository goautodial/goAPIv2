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
 
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass'])) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']);   
    
	// ERROR CHECKING 
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}		

			//$query = "SELECT s.server_id, s.server_description, s.server_ip, s.active, s.sysload, s.channels_total, s.cpu_idle_percent, s.disk_usage, su.last_update as s_time,UNIX_TIMESTAMP(su.last_update) as u_time FROM servers s, server_updater su WHERE s.server_ip=su.server_ip LIMIT 100";
			$astDB->join("server_updater su", "s.server_ip=su.server_ip", "LEFT");
			$rsltv										= $astDB->get("servers s", 100, "s.server_id, s.server_description, s.server_ip, s.active, s.sysload, s.channels_total, s.cpu_idle_percent, s.disk_usage, su.last_update as s_time,UNIX_TIMESTAMP(su.last_update) as u_time");			
			$countResult = $astDB->getRowCount();

			if ($astDB->count > 0) {			
				$data 									= array();
				
				foreach ($rsltv as $fresults){
					array_push($data, $fresults);
				}
				
				$apiresults 							= array(
					"result" 								=> "success", 
					//"query"									=> $astDB->getLastQuery(),
					"data" 									=> $data
				);
			} 
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
