<?php
/**
 * @file 		goGetAllUsers.php
 * @brief 		API to get all User Lists 
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
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
    include_once ("goAPI.php");
    include_once ("../licensed-conf.php");
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
	if(!empty($session_user)){
		// get user_level
		$astDB->where("user", $log_user);
		$query_userlevel = $astDB->getOne("vicidial_users", "user_level");
		$user_level = $query_userlevel["user_level"];
		
		if (!checkIfTenant($log_group, $goDB)) {
			if (strtoupper($log_group) != 'ADMIN') {
				if ($user_level > 8) {
					$astDB->where("user_group", $log_group);
				}
			}
		} else { 
			$astDB->where("user_group", $log_group);
		}
		// getting agent count	
		$astDB->where("user", DEFAULT_USERS, "NOT IN");
		$astDB->where("user_level", 4, "!=");
		$astDB->orderby("user", "asc");
		$getLastCount = $astDB->get("vicidial_users", NULL, "user");
		$max = $astDB->count;
		//$getLastCount = "SELECT user FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI', 'goautodial', '$user') AND user_level != '4' ORDER BY user ASC";
			
		// condition
		for($i=0; $i < $max; $i++){
			$userRow = $getLastCount[$i];
			if(preg_match("/^agent/i", $userRow['user'])){
				$get_last = preg_replace("/^agent/i", "", $userRow['user']);
				$last_num[] = intval($get_last);
			}
		}

		// return data
		$get_last = max($last_num);
		$agent_num = $get_last + 1;
			
		// getting phone login count
		
		$astDB->where("user", DEFAULT_USERS, "NOT IN");
		$astDB->where("user_level", 4, "!=");
		$astDB->orderby("phone_login", "desc");
		$queryLastPhoneLogin = $astDB->get("vicidial_users", NULL, "phone_login");		
		//$queryLastPhoneLogin = $astDB->rawQuery("SELECT phone_login FROM vicidial_users WHERE user NOT IN (?,?,?,?) AND user_level != ? AND phone_login != ? $notAdminSQL ORDER BY phone_login DESC", $arrLastPhoneLogin);
		
		// condition
		if($astDB->count > 0){
			for($i=0; $i < count($queryLastPhoneLogin);$i++){
				$get_last_phonelogin = $queryLastPhoneLogin[$i];
				if(preg_match("/^Agent/i", $get_last_phonelogin['phone_login'])){
					$get_last_count = preg_replace("/^Agent/i", "", $get_last_phonelogin['phone_login']);
					$last_pl[] = intval($get_last_count);
				}else{
					$get_last_count = $get_last_phonelogin['phone_login'];
					$last_pl[] = intval($get_last_count);
				}
			}
			// return data
			$phonelogin_num = max($last_pl);
			$phonelogin_num = $phonelogin_num + 1;
			
		}else{
			// return data
			$phonelogin_num = "0000001";
		}
	
		// getting all users
		//	$query = "SELECT user_id, user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC;";
		//$getting_users = array("VDAD", "VDCL", "goAPI", "goautodial", 4, $user);
		$cols = array("user_id", "user", "full_name", "user_level", "user_group", "phone_login", "active");
		$astDB->where("user", DEFAULT_USERS, "NOT IN");
		$astDB->where("user_level", 4, "!=");
		$astDB->orderby("user", "asc");
		$query = $astDB->get("vicidial_users", NULL, $cols);
		$countResult = $astDB->count;	

		$goDB->orderby("userid", "desc");
		$querygo = $goDB->get("users", NULL, "userid,avatar");	
		$countResultgo = $goDB->count;
		//$querygo = $goDB->rawQuery("SELECT userid, avatar FROM users ORDER BY userid DESC");		
		
		// condition	
		if($countResultgo > 0) {
			$datago = array();
			for($i=0; $i < $countResultgo; $i++){
				$fresultsgo = $querygo[$i];					
				array_push($datago, $fresultsgo);
				$dataUserIDgo[] = $fresultsgo['userid'];
				$dataAvatar[] = $fresultsgo['avatar'];
			}
		}
		
		// condition
		if($countResult > 0) {
			$data = array();
			for($i=0; $i < $countResult; $i++){
				$fresults = $query[$i];
				array_push($data, $fresults);
				$dataUserID[] = $fresults['user_id'];
				$dataUser[] = $fresults['user'];
				$dataFullName[] = $fresults['full_name'];
				$dataUserLevel[] = $fresults['user_level'];
				$dataUserGroup[] = $fresults['user_group'];
				$dataPhone[] = $fresults['phone_login'];
				$dataActive[]	= $fresults['active'];
				//$apiresults = array("result" => "success", "data" => $data);
			}
			
			$apiresults = array("result" => "success", "user_id" => $dataUserID,"user_group" => $dataUserGroup, "user" => $dataUser, "full_name" => $dataFullName, "user_level" => $dataUserLevel, "phone_login" => $dataPhone, "active" => $dataActive, "last_count" => $agent_num, "last_phone_login" => $phonelogin_num, "avatar" => $dataAvatar, "useridgo" => $dataUserIDgo, "licensedSeats" => $config["licensedSeats"]);
			
		} else {
			$err_msg = error_handle("10010");
			$apiresults = array("code" => "10010", "result" => $err_msg); 
		}
	}else{
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg); 
	}
?>
