<?php
    #######################################################
    #### Name: goUserLogin.php	                       ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
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
		
			$SQLdate = date("Y-m-d H:i:s");
			$log_id = log_action('LOGIN', $dataUser, $ip_address, $SQLdate, "User $dataUser logged-in", $dataUserGroup);
			//$logQuery = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,user_group) values('$dataUser','$ip_address','$SQLdate','LOGIN','User $dataUser logged-in','$dataUserGroup');";
			//mysqli_query($linkgo, $logQuery);
			
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
				"avatar" => $dataAvatar
			);
		}
	} else {
		$apiresults = array("result" => "Error: Invalid login credentials please try again.");
	}
	
?>
