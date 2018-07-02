<?php
/**
 * @file 		goDeleteUser.php
 * @brief 		API to delete specific User 
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
**/

    @include_once ("goAPI.php");
    
    // POST or GET Variables
    $user_id = $astDB->escape($_REQUEST['user_id']);        
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);

	if (empty($user_id)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	} elseif (empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	} else {
		if (!checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_id", $user_id);
			//$ul = "WHERE user_id='$user_id'";
		} else {
			$astDB->where("user_id", $user_id);
			$astDB->where("user_group", $log_group);
		}		
		
		$query = $astDB->getOne("vicidial_users");
		
		if($astDB->count > 0) {
			$error_count = 0;
			$phone_login = $query['phone_login'];
			
			$astDB->where("user_id", $user_id);
			$q_deleteUser = $astDB->delete("vicidial_users");
			
			$astDB->where("extension", $phone_login);
			$q_deletePhone = $astDB->delete("phones");
			
			$goDB->where("userid", $user_id);
			$qgo_deleteUser = $goDB->delete("users");
			
			$kamDB->where("username", $phone_login);
			$qkam_deletePhone = $kamDB->delete("subscriber");
			
			$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted User: $user_id", $log_group, $q_deleteUser. $q_deletePhone . $qgo_deleteUser . $qkam_deletePhone);
		} else {
			$error_count = 1;
		}
		
		if ($error_count == 0) { $apiresults = array("result" => "success"); }		
		if ($error_count == 1) {
			$err_msg = error_handle("10010");
			$apiresults = array("code" => "10010", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Delete Failed");
		}
	}
	
?>
