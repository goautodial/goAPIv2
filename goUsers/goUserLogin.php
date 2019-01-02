<?php
/**
 * @file        goUserLogin.php
 * @brief       API used to login to application
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author      Noel Umandap
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
    
    ### POST or GET Variables
	$user_name 											= $astDB->escape($_REQUEST['user_name']);
	$user_pass 											= $astDB->escape($_REQUEST['user_pass']);
	$ip_address 										= $astDB->escape($_REQUEST['ip_address']);
	$pass_hash 											= '';
	$cwd 												= $_SERVER['DOCUMENT_ROOT'];
	$auth 												= 0;

	##### START SYSTEM_SETTINGS LOOKUP #####
	$rsltp 												= $astDB->rawQuery("SELECT use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings FROM system_settings;");
	$qm_conf_ct 										= $astDB->getRowCount();
	
	if ($qm_conf_ct > 0) {
		$rowp 											= $rsltp[0];
		$non_latin 										= $rowp['use_non_latin'];
		$SSwebroot_writable 							= $rowp['webroot_writable'];
		$SSpass_hash_enabled 							= $rowp['pass_hash_enabled'];
		$SSpass_key 									= $rowp['pass_key'];
		$SSpass_cost 									= $rowp['pass_cost'];
		$SShosted_settings 								= $rowp['hosted_settings'];
	}
	##### END SETTINGS LOOKUP #####
	###########################################
	
	$goDB->where('setting', 'GO_agent_domain');
	$rsltg 												= $goDB->getOne('settings', 'value');
	$realm 												= (!is_null($rsltg['value']) || $rsltg['value'] !== '') ? $rsltg['value'] : 'goautodial.com';
	
    ### Check if user_name or user_email
	if (!empty($user_name)) {
		$astDB->where("user", $user_name);
	} else {
		$astDB->where("email", $user_name);
	}
	
	if ($SSpass_hash_enabled > 0) {
		if ($bcrypt < 1) {
			$pass_hash 									= encrypt_passwd($user_pass, $SSpass_cost, $SSpass_key);
		} else {
			$pass_hash 									= $user_pass;
		}
		
		$astDB->where("pass_hash", $pass_hash);		
	} else {
		$astDB->where('pass', $pass_hash);
	}

	$cols												= array("user_id", "user", "email", "pass", "full_name", "user_level", "user_group", "active", "pass_hash", "phone_login", "phone_pass");
	$rsltv 												= $astDB->get("vicidial_users", 1, $cols);
	var_dump($rsltv);
	if	($rsltv > 0) {
		foreach ($rsltv as $fresults){
			$dataUser 									= $fresults['user'];
			$dataFullName 								= $fresults['full_name'];
			$dataUserLevel 								= $fresults['user_level'];
			$dataUserGroup 								= $fresults['user_group'];
			$dataActive   								= $fresults['active'];
			$dataUserId 								= $fresults['user_id'];
			$dataEmail 									= $fresults['email'];
			$dataPhone_login 							= $fresults['phone_login'];
			$dataPhone_pass 							= $fresults['phone_pass'];
			$dataPass 									= ($SSpass_hash_enabled > 0) ? $fresults['pass_hash'] : $fresults['pass'];
			
			$avatar 									= $goDB										
				->where("user_id", $dataUserId)
				->get("go_avatars", NULL, "*");
			
			$dataAvatar 								= ($avatar) ? "./php/ViewImage.php?user_id=$dataUserId" : "";		
			$log_id 									= log_action($goDB, 'LOGIN', $dataUser, $ip_address, "User $dataUser logged-in", $dataUserGroup);			
			$seenResult 								= $goDB->rawQuery("SHOW COLUMNS FROM `users` LIKE 'last_seen_date'");
			
			if ($goDB->getRowCount() > 0) {
				$rsltu 									= $goDB->rawQuery("UPDATE users SET last_seen_date='".date("Y-m-d H:i:s")."' WHERE name='$dataUser';");
			}
			
			### Check if someone is logged in on phone but agent is not live
			if ($dataUserLevel < 7) {
				/*$kamQ = "SELECT * FROM location WHERE username='$dataPhone_login';";
				$rsltQ = $kamDB->rawQuery($kamQ);
				$isLoggedIn = $kamDB->getRowCount();
	
				$astQ = "SELECT * FROM vicidial_live_agents WHERE user='$dataUser';";
				$rsltQ = $astDB->rawQuery($astQ);
				$isLive = $astDB->getRowCount();*/
				
				$isLoggedIn 							= $kamDB
					->where("username", $dataPhone_login)
					->get("location", NULL, "*");
			
				$isLive 								= $astDB
					->where("user", $dataUser)
					->get("vicidial_live_agents", NULL, "*");
				
				if (($isLoggedIn < 1 && $isLive > 0) || ($isLoggedIn > 0 && $isLive < 1)) {
					//$astQ = "DELETE FROM go_agent_sessions WHERE sess_agent_user='$dataUser' LIMIT 1;";
					//$rsltQ  = $astDB->rawQuery($astQ);
					
					$astDB->where("sess_agent_user", $dataUser);
					$astDB->delete("go_agent_sessions");
					
					$log_id 							= log_action($goDB, 'FORCE-LOGOUT', $dataUser, $ip_address, "User $dataUser used emergency log out upon login.", $dataUserGroup);
				}
			}
			### End checker
			
			$goDB->where('setting', 'GO_agent_use_wss');
			$rsltw 										= $goDB->getOne('settings', 'value');
			$use_webrtc 								= (!is_null($rsltw['value']) || $rsltw['value'] !== '') ? $rsltw['value'] : 0;
			
			$apiresults 								= array(
				"result" 									=> "success",
				"user_group" 								=> $dataUserGroup,
				"userno" 									=> $dataUser,
				"full_name" 								=> $dataFullName,
				"user_level" 								=> $dataUserLevel,
				"active" 									=> $dataActive,
				"user_id" 									=> $dataUserId,
				"email" 									=> $dataEmail,
				"pass" 										=> $dataPass,
				"bcrypt" 									=> $SSpass_hash_enabled,
				"salt" 										=> $SSpass_key,
				"cost" 										=> $SSpass_cost,
				"phone_login" 								=> $dataPhone_login,
				"phone_pass" 								=> $dataPhone_pass,
				"avatar" 									=> $dataAvatar,
				"realm" 									=> $realm,
				"use_webrtc" 								=> $use_webrtc
			);
		}
	} else {
		$rslt 											= $astDB
			->where("user", $user_name)
			->getOne("vicidial_users", "user_group");
		
		$thisGroup 										= (strlen($rslt['user_group']) > 0) ? $rslt['user_group'] : "";
		$log_id 										= log_action($goDB, 'LOGIN', $user_name, $ip_address, "User $user_name failed to logged-in", $thisGroup);
		
		$apiresults 									= array(
			"result" 										=> "error", 
			"message" 										=> "Invalid login credentials please try again."
		);
	}
	
?>
