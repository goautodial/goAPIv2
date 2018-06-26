<?php
/**
 * @file 		goGetServerInfo.php
 * @brief 		API to get specific server
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho
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
	$server_id = $astDB->escape($_REQUEST["server_id"]);

	if($server_id == null) {
			$apiresults = array("result" => "Error: Set a value for Server ID.");
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			//$astDB->where('user_group', $log_group);
		}

		$astDB->where("server_id", $server_id);
		$rsltv = $astDB->getOne('servers');	
				
		if(!empty($rsltv)) {
			$apiresults = array("result" => "success", "data" => $rsltv);
		} else {
			$apiresults = array("result" => "Error: Server does not exist.");
		}
	}
?>
