<?php
    ///////////////////////////////////////////////////////
    /// Name: goGetAllUserLists.php 		///
    /// Description: API to get all User Lists 		///
    /// Version: 4.0 		///
    /// Copyright: GOAutoDial Ltd. (c) 2011-2016 		///
    /// Written by: Jeremiah Sebastian V. Samatra 		///
    /// License: AGPLv2 		///
    //////////////////////////////////////////////////////
    include_once ("../goFunctions.php");
    include_once ("../licensed-conf.php");
	
	$user = $session_user;
	
	if(!empty($session_user)){
		// get user_level
		//$query_userlevel_sql = "SELECT user_level,user_group FROM vicidial_users WHERE user = '$user' LIMIT 1";
		$astDB->where('user', $user);
		$fetch_user_level = $astDB->getOne('vicidial_users', 'user_level,user_group');
		$user_level = $fetch_user_level["user_level"];
		$groupId = $fetch_user_level["user_group"];
		
		if (!checkIfTenant($groupId, $goDB)) {
			$ul='';
			if ($groupId != 'ADMIN') {
				if ($user_level > 8) {
					//$uQuery = "SELECT tenant_id FROM go_multi_tenant;";
					$uRslt = $goDB->get('go_multi_tenant');
					if ($goDB->getRowCount() > 0) {
						$ul = "AND user_group NOT IN (";
						$uListGroups = "";
						foreach ($uRslt as $uResults) {
							$uListGroups = "'{$uResults['tenant_id']}',";
						}
						$ul .= rtrim($uListGroups, ',');
						$ul .= ")";
					}
				} else {
					$ul = "AND user_group='$groupId'";
				}
			}
		} else { 
			$ul = "AND user_group='$groupId'";  
		}
		if ($groupId != 'ADMIN') {
			$notAdminSQL = "AND user_group != 'ADMIN'";
		}
		
		// getting agent count
		$getLastCount = "SELECT user FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI', 'goautodial', '$user') AND user_level != '4' ORDER BY user ASC";
		$queryCount = $astDB->rawQuery($getLastCount);
		$max = $astDB->getRowCount();
			
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
		$getLastPhoneLogin = "SELECT phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI', 'goautodial') AND user_level != '4' AND phone_login != '' $notAdminSQL ORDER BY phone_login DESC";
		$queryPhoneLoginCount = $astDB->rawQuery($getLastPhoneLogin);
		$max_phonelogins = $astDB->getRowCount();
		
			// condition
			if($max_phonelogins > 0){
				foreach ($queryPhoneLoginCount as $get_last_phonelogin){
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
		$query = "SELECT user_id, user, full_name, user_level, user_group, phone_login, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goAPI','goautodial') AND (user_level != '4' AND user_level <= '$user_level') $ul ORDER BY user != '$user', user_id DESC";
		$rsltv = $astDB->rawQuery($query);
			$countResult = $astDB->getRowCount();
			
			$querygo = "SELECT userid, avatar FROM users ORDER BY userid DESC";
			$rsltvgo = $goDB->rawQuery($querygo);
			$countResultgo = $goDB->getRowCount();
			
			if($countResultgo > 0) {
				$datago = array();
				foreach ($rsltvgo as $fresultsgo){
					array_push($datago, $fresultsgo);
					$dataUserIDgo[] = $fresultsgo['userid'];
					$dataAvatar[] = $fresultsgo['avatar'];
				}
			}               
			
		// condition
		if($countResult > 0) {
				$data = array();
				foreach ($rsltv as $fresults){
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
