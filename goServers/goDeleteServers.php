<?php
 /**
 * @file 		goDeleteServers.php
 * @brief 		API to delete servers
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
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
 
	$log_user 						= $session_user;
	$log_group 						= go_get_groupid($session_user, $astDB); 
	$log_ip 						= $astDB->escape($_REQUEST['log_ip']);
    
	### POST or GET Variables
	$server_id 						= $astDB->escape($_REQUEST['server_id']);
	
    ### Check Server ID if its null or empty
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif ($server_id == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Server ID."
		);
	} else {		
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}
		
		$astDB->where("server_id", $server_id);
		$astDB->getOne("servers");

		if($astDB->count > 0) {
			$astDB->where("server_id", $server_id);
			$astDB->delete("servers");
			
			$log_id 				= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Server ID: $server_id", $log_group, $astDB->getLastQuery());			
			$apiresults 			= array("result" => "success");
		} else {
			$apiresults				= array("result" => "Error: Server doesn't exist.");
		}
	}
?>
