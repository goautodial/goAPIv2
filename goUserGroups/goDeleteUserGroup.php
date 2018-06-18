<?php
/**
 * @file 		goDeleteUserGroup.php
 * @brief 		API to delete specific User Group
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com>
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
    include_once ("goAPI.php");
    
    // POST or GET Variables
    $user_group = $astDB->escape($_REQUEST['user_group']);
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
    
    if(!isset($session_user) || is_null($session_user)){
    	$apiresults = array("result" => "Error: Missing Required Parameters.");
    }elseif(is_null($user_group)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User Group."); 
	} else {
		$groupId = $log_group;
		if (!checkIfTenant($groupId)) {
			$astDB->where("user_group", $user_group);
			//$ul = "WHERE user_group='$user_group'";
		} else {
			$astDB->where("user_group", $user_group);
			$astDB->where("user_group", $groupId);
			//$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";  
		}
		
		$fresults = $astDB->getOne("vicidial_user_groups", "user_group");
		//$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$countResult = $astDB->count;
		
		if($countResult > 0) {
			$dataUserGroup = $fresults['user_group'];
						
			if(!$dataUserGroup == null) {
				$astDB->where("user_group", $dataUserGroup);
				$astDB->where("user_group", "ADMIN", "!=");
				$astDB->delete("vicidial_user_groups");
				//$deleteQuery = "DELETE FROM vicidial_user_groups WHERE user_group='$dataUserGroup' AND user_group != 'ADMIN';";

				$goDB->where("user_group", $dataUserGroup);
				$goDB->where("user_group", "ADMIN", "!=");
				$goDB->delete("user_access_group");
				//$deleteQueryA = "DELETE FROM user_access_group WHERE user_group='$dataUserGroup' AND user_group != 'ADMIN';";
				
			} else {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "Error: User Group doesn't exist.");
			}
			
		} else {
			$err_msg = error_handle("41004", "user_group. Does not exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: User Group doesn't exist.");
		}
		
		if (!checkIfTenant($groupId)) {
			$astDB->where("user_group", $user_group);
		} else {
			$astDB->where("user_group", $user_group);
			$astDB->where("user_group", $groupId);
		}

		$astDB->getOne("vicidial_user_groups", "user_group");
		//$query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		//$rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
		$countResult = $astDB->count;
		
		if($countResult > 0) {
			$err_msg = error_handle("41004", "user_group");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		}else{
			$apiresults = array("result" => "success");
			$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted User Group: $dataUserGroup", $log_group, $deleteQuery);
		}
	}//end
?>
