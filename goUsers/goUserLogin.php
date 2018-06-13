<?php
/**
 * @file        goUserLogin.php
 * @brief       API used to login to application
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
 * @author      Noel Umandap <noelumandap@goautodial.com>
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
	$user_name = $_REQUEST['user_name'];
	//$user_email = $_REQUEST['user_email'];
	$user_pass = $_REQUEST['user_pass'];
	$ip_address = $_REQUEST['ip_address'];
	$pass_hash = '';
	$cwd = $_SERVER['DOCUMENT_ROOT'];
	$auth = 0;

	##### START SYSTEM_SETTINGS LOOKUP #####
	$rsltp = mysqli_query($link, "SELECT use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings FROM system_settings;");
	$qm_conf_ct = mysqli_num_rows($rsltp);
	if ($qm_conf_ct > 0) {
		$rowp = mysqli_fetch_array($rsltp, MYSQLI_ASSOC);
		$non_latin =            $rowp['use_non_latin'];
		$SSwebroot_writable =   $rowp['webroot_writable'];
		$SSpass_hash_enabled =  $rowp['pass_hash_enabled'];
		$SSpass_key =           $rowp['pass_key'];
		$SSpass_cost =          $rowp['pass_cost'];
		$SShosted_settings =    $rowp['hosted_settings'];
	}
	##### END SETTINGS LOOKUP #####
	###########################################
	
    ### Check if user_name or user_email
	if(!empty($user_name)){
		//username
		$user = "user='".$user_name."'";
	}else{
		//email
		$user = "email='".$user_name."'";
	}
	
    $passSQL = "pass='$user_pass'";
	if ($SSpass_hash_enabled > 0) {
		if ($bcrypt < 1) {
			$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$user_pass");
			$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
		} else {$pass_hash = $user_pass;}
		$passSQL = "pass_hash='$pass_hash'";
		//$aDB->where('pass_hash', $pass_hash);
	}
	
	$query = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
	$rsltg = mysqli_query($linkgo, $query);
	$rowg = mysqli_fetch_array($rsltg, MYSQLI_ASSOC);
	$realm = (!is_null($rowg['value']) || $rowg['value'] !== '') ? $rowg['value'] : 'goautodial.com';
	
	$query = "SELECT user_id, user, email, pass, full_name, user_level, user_group, active, pass_hash, phone_login, phone_pass
			  FROM vicidial_users
			  WHERE ".$user."
			  AND ".$passSQL."
			  ORDER BY user ASC
			  LIMIT 1;";
	$rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);

	if($countResult > 0) {
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataUser = $fresults['user'];
			$dataFullName = $fresults['full_name'];
			$dataUserLevel = $fresults['user_level'];
			$dataUserGroup = $fresults['user_group'];
			$dataActive   = $fresults['active'];
			$dataUserId = $fresults['user_id'];
			$dataEmail = $fresults['email'];
			$dataPhone_login = $fresults['phone_login'];
			$dataPhone_pass = $fresults['phone_pass'];
			$dataPass = ($SSpass_hash_enabled > 0) ? $fresults['pass_hash'] : $fresults['pass'];
			
			$rslti = mysqli_query($linkgo, "SELECT * FROM go_avatars WHERE user_id='$dataUserId';");
			$dataAvatar = (mysqli_num_rows($rslti) > 0) ? "./php/ViewImage.php?user_id=$dataUserId" : "";
		
			$log_id = log_action($linkgo, 'LOGIN', $dataUser, $ip_address, "User $dataUser logged-in", $dataUserGroup);
			//$logQuery = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,user_group) values('$dataUser','$ip_address','$SQLdate','LOGIN','User $dataUser logged-in','$dataUserGroup');";
			//mysqli_query($linkgo, $logQuery);
			
			$seenResult = mysqli_query($linkgo, "SHOW COLUMNS FROM `users` LIKE 'last_seen_date'");
			if (mysqli_num_rows($seenResult) > 0) {
				$rsltu = mysqli_query($linkgo, "UPDATE users SET last_seen_date='".date("Y-m-d H:i:s")."' WHERE name='$dataUser';");
			}
			
			### Check if someone is logged in on phone but agent is not live
			if ($dataUserLevel < 7) {
				$kamQ = "SELECT * FROM location WHERE username='$dataPhone_login';";
				$rsltQ = mysqli_query($linkgokam, $kamQ);
				$isLoggedIn = mysqli_num_rows($rsltQ);
				
				$astQ = "SELECT * FROM vicidial_live_agents WHERE user='$dataUser';";
				$rsltQ = mysqli_query($link, $astQ);
				$isLive = mysqli_num_rows($rsltQ);
				
				if (($isLoggedIn < 1 && $isLive > 0) || ($isLoggedIn > 0 && $isLive < 1)) {
					$astQ = "DELETE FROM go_agent_sessions WHERE sess_agent_user='$dataUser' LIMIT 1;";
					$rsltQ  = mysqli_query($link, $astQ);
					
					$log_id = log_action($linkgo, 'FORCE-LOGOUT', $dataUser, $ip_address, "User $dataUser used emergency log out upon login.", $dataUserGroup);
				}
			}
			### End checker
			
			$apiresults = array(
				"result" => "success",
				"user_group" => $dataUserGroup,
				"userno" => $dataUser,
				"full_name" => $dataFullName,
				"user_level" => $dataUserLevel,
				"active" => $dataActive,
				"user_id" => $dataUserId,
				"email" => $dataEmail,
				"pass" => $dataPass,
				"bcrypt" => $SSpass_hash_enabled,
				"salt" => $SSpass_key,
				"cost" => $SSpass_cost,
				"phone_login" => $dataPhone_login,
				"phone_pass" => $dataPhone_pass,
				"avatar" => $dataAvatar,
				"realm" => $realm
			);
		}
	} else {
		$query = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user='$user_name';");
		$rslt = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$thisGroup = (strlen($rslt['user_group']) > 0) ? $rslt['user_group'] : "";
		$log_id = log_action($linkgo, 'LOGIN', $user_name, $ip_address, "User $user_name failed to logged-in", $thisGroup);
		
		$apiresults = array("result" => "error", "message" => "Invalid login credentials please try again.");
	}
	
?>
