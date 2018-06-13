<?php
/**
 * @file 		goDeleteUser.php
 * @brief 		API to delete specific User 
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

    @include_once ("goAPI.php");
    
    // POST or GET Variables
    $user_id = $astDB->escape($_REQUEST['user_id']);
	$user = $astDB->escape($_REQUEST['user']);
	//$action = $_REQUEST['action'];
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    
	$log_user = $session_user;
	$groupId = go_get_groupid($session_user, $astDB);

	if(empty($user_id) && empty($user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	} elseif(empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for User ID."); 
	}else {
		
		if (!checkIfTenant($groupId, $goDB)) {
			$ul = "AND user_id=?";
			$arr_ul = array($user_id);
		} else { 
			$ul = "AND user_id=? AND user_group=?";
			$arr_ul = array($user_id, $groupId);  
		}
		if ($groupId != 'ADMIN') {
			$notAdminSQL = "AND user_group != 'ADMIN'";
			$arr_notAdminSQL = array("ADMIN");
		}
		
		//if($action == "delete_selected"){
			if(!empty($user)){
				$exploded = explode(",",$user);
			}else{
				$exploded = explode(",",$user_id);
			}
			
			$error_count = 0;
			$string_return = "";
			$test = array();

			//$arr_select = array();
			//array_push($default_users, 4, "");
			for($i=0;$i < count($exploded);$i++){
				if(!empty($user)){
				//$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN (?,?,?,?) AND user_level != ? AND user = '".$exploded[$i]."';";
					//$arr_select[5] = $exploded[$i];
					//$selectQuery = $astDB->rawQuery("SELECT user,phone_login FROM vicidial_users WHERE user NOT IN (?,?,?,?) AND user_level != ? AND user = ?;", $arr_select);
					$astDB->where("user", DEFAULT_USERS, "NOT IN");
					$astDB->where("user_level", 4);
					$astDB->where("user", $exploded[$i]);
					$selectQuery = $astDB->getOne("vicidial_users", "user, phone_login");
				}else{
					//$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' AND user_id = '".$exploded[$i]."';";
					//$arr_select[5] = $exploded[$i];
					$astDB->where("user", DEFAULT_USERS, "NOT IN");
					$astDB->where("user_level", 4);
					$astDB->where("user_id", $exploded[$i]);
					$selectQuery = $astDB->getOne("vicidial_users", "user, phone_login");
					//$selectQuery = $astDB->rawQuery("SELECT user,phone_login FROM vicidial_users WHERE user NOT IN (?,?,?,?) AND user_level != ? AND user_id = ?;", $arr_select);	
				}
				
				//$selectResult = mysqli_query($link, $selectQuery) or die(mysqli_error($link));
				$numResult = $astDB->count;
				//array_push($test, $selectQuery);
				if($numResult > 0){
					//while($fresults = mysqli_fetch_array($selectResult)){
						$dataUserID = $selectQuery["user"];
						$dataPhoneLogin = $selectQuery["phone_login"];
						
						if($groupId !== "ADMIN")
							$astDB->where("user_group", "ADMIN", "!=");
						$astDB->where("user", $dataUserID);
						$deleteQuery = $astDB->delete("vicidial_users");
						//"DELETE FROM vicidial_users WHERE user='$dataUserID' $notAdminSQL";
						
						$goDB->where("name", $dataUserID);
						$deleteQueryGo = $goDB->delete("users");
						//"DELETE FROM users WHERE name='$dataUserID'";

						$astDB->where("extension", $dataPhoneLogin);
						$deleteQueryA = $astDB->delete("phones");
						//"DELETE FROM phones WHERE extension='$dataPhoneLogin';";
						
						$kamDB->where("username", $dataPhoneLogin);
						$deleteQueryCB = $kamDB->delete("phones");
						//"DELETE FROM subscriber WHERE username='$dataPhoneLogin';";
					//}
				}else{
					$error_count = $error_count + 1;
				}
				
				$astDB->where("user", $dataUserID);
				$check_del = $astDB->query("vicidial_users", "user");
				//$querydel = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' AND user='$dataUserID' $notAdminSQL ORDER BY user ASC LIMIT 1;";
				$countResult = $astDB->count;
				
				if($countResult > 0) {
					$error_count = $error_count + 1;
				}
				
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User: $dataUserID", $groupId, $deleteQuery);
				
			}
			
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Delete Failed");
			} else {
				$apiresults = array("result" => "success", "query" => $test); 
			}
		/*	
		}else{
			if(!empty($user)){
				$ul = "AND user = '$user'";
			}else{
				$ul = "AND user_id = '$user_id'";
			}
			
			$selectQuery = "SELECT user,phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' $ul;";
			$selectResult = mysqli_query($link, $selectQuery) or die(mysqli_error($link));
			$numResult = mysqli_num_rows($selectResult);
			
			if($numResult > 0){
				
				while($fresults = mysqli_fetch_array($selectResult)){
					$dataUserID = $fresults["user"];
					$dataPhoneLogin = $fresults["phone_login"];
				}
				
				$deleteQuery = "DELETE FROM vicidial_users WHERE user='$dataUserID' AND user != 'ADMIN'"; 
				$deleteResult = mysqli_query($link, $deleteQuery) or die(mysqli_error($link));
				
				$deleteQueryGo = "DELETE FROM users WHERE name='$dataUserID'"; 
				$deleteResultGo = mysqli_query($linkgo, $deleteQueryGo) or die(mysqli_error($linkgo));
				
				$deleteQueryA = "DELETE FROM phones WHERE extension='$dataPhoneLogin';";
				$deleteResultA = mysqli_query($link, $deleteQueryA) or die(mysqli_error($link));
				
				$deleteQueryCB = "DELETE FROM subscriber WHERE username='$dataPhoneLogin';";
				$deleteResultCB = mysqli_query($linkgokam, $deleteQueryCB) or die(mysqli_error($linkgokam));
				
				$querydel = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goautodial','goAPI') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
				$rsltvdel = mysqli_query($link, $querydel);
				$countResult = mysqli_num_rows($rsltvdel);
				
				if($countResult > 0) {
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Delete Failed");
				} else {
					$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted User: $dataUserID", $log_group, $deleteQuery);
					
					$apiresults = array("result" => "success"); 
				}
			}else{
				$err_msg = error_handle("41004");
				$apiresults = array("code" => "41004", "result" => $err_msg);
			}
		}*/
	}
?>
