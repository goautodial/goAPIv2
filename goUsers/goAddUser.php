<?php
   ####################################################
   #### Name: goAddUser.php                        ####
   #### Description: API to add new user           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   ####	Updated by: Alexander Jim Abenoja          ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");

    ### POST or GET Variables
       // $values = $_REQUEST['items'];
        $user = $_REQUEST['user'];
        $pass = $_REQUEST['pass'];
        $full_name = $_REQUEST['full_name'];
        $phone_login = $_REQUEST['phone_login'];
        $phone_pass = $_REQUEST['pass'];
        $user_group = $_REQUEST['user_group'];
        $active = strtoupper($_REQUEST['active']);
        $seats = $_REQUEST['seats'];

	$goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];

    ### Default values 
        $defActive = array("Y","N");

    ### Error Checking
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user)){
		$apiresults = array("result" => "Error: Special characters found in user");
	} else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
		$apiresults = array("result" => "Error: Special characters found in password");
	} else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $full_name)){
		$apiresults = array("result" => "Error: Special characters found in full_name");
	} else {
        if($user == null) {
                $apiresults = array("result" => "Error: Set a value for User.");
        } else {
        if($pass == null) {
                $apiresults = array("result" => "Error: Set a value for password.");
        } else {
        if($full_name == null) {
                $apiresults = array("result" => "Error: Set a value for Full name.");
        } else {
        if($user_group == null) {
                $apiresults = array("result" => "Error: Set a value for User Group.");
        } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                $groupId = go_get_groupid($goUser);
                if (!checkIfTenant($groupId)) {
                        $ulUser = "AND user='$user'";
			$ulug = "WHERE user_group='$user_group'";
                } else {
                        $ulUser = "AND user='$user' AND user_group='$groupId'";
			$ulug = "WHERE user_group='$user_group' AND user_group='$groupId'";
                }

                $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {
		$queryUserCheck = "SELECT user, full_name, user_level, user_group, phone_login, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' AND '$phone_login' = (SELECT phone_login FROM vicidial_users WHERE phone_login = '$phone_login')  $ulUser ORDER BY user ASC LIMIT 1;";
                $rsltvCheck = mysqli_query($link, $queryUserCheck);
                $countCheckResult = mysqli_num_rows($rsltvCheck);

                if($countCheckResult <= 0) {
		
		$querygetserverip = "select server_ip from servers;";
		$rsltserverip = mysqli_query($link, $querygetserverip);
		$rServerIP = mysqli_fetch_array($rsltserverip, MYSQLI_ASSOC);
		$server_ip = $rServerIP['server_ip'];

			if($user_group == "ADMIN" || $user_group == "admin"){
                                $user_level = 9;
                                $phone_pass = "";
				$phone_login = "";
				$agentcall_manual = 1;
				$agentonly_callbacks = 1;
                         } else {
				$user_level = 1;
				$agentcall_manual = 1;
				$agentonly_callbacks = 1;
				
                         }

		$cwd = $_SERVER['DOCUMENT_ROOT'];
 		$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
                $pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
		$queryUserAdd = "INSERT INTO  vicidial_users (user, pass, user_group, full_name, user_level, phone_login, phone_pass, agentonly_callbacks, agentcall_manual, active, vdc_agent_api_access,pass_hash)
						VALUES ('$user', '$pass', '$user_group', '$full_name', '$user_level', '$phone_login', '$phone_pass', '$agentonly_callbacks', '$agentcall_manual', '$active', '1', '$pass_hash');";
		$resultQueryAddUser = mysqli_query($link, $queryUserAdd);

	### Admin logs
		$SQLdate = date("Y-m-d H:i:s");
		$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New User $user','INSERT INTO vicidial_users (user,pass,full_name,phone_login,phone_pass,user_group,active) VALUES ($user,$pass,$full_name,$phone_login,$phone_pass,$user_group,$active)');";
		$rsltvLog = mysqli_query($linkgo, $queryLog);

		$queryUserCheckAgain = "SELECT user  FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ulUser ORDER BY user ASC LIMIT 1;";
//		$queryUserCheckAgain = "SELECT user  FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' AND user='agent093' ORDER BY user ASC LIMIT 1;";
		$rsltvCheckAgain = mysqli_query($link, $queryUserCheckAgain);
		$countCheckResultAgain = mysqli_num_rows($rsltvCheckAgain);
                
		if($countCheckResultAgain > 0) {



			$queryInsertUser = "INSERT INTO `phones` (`extension`,  `dialplan_number`,  `voicemail_id`,  `phone_ip`,  `computer_ip`,  `server_ip`,  `login`,  `pass`,  `status`,  `active`,  `phone_type`,  `fullname`,  `company`,  `picture`,  `protocol`,  `local_gmt`,  `outbound_cid`,  `template_id`,  `conf_override`,  `user_group`,  `conf_secret`,  `messages`,  `old_messages`) VALUES ('$phone_login',  '9999$phone_login',  '$phone_login',  '',  '', '$server_ip',  '$phone_login',  '$phone_pass',  'ACTIVE',  '$active',  '',  '$full_name',  '$user_group',  '',  'EXTERNAL',  '-5',  '0000000000',  '--NONE--',  '$conf_override',  '$user_group',  '$phone_pass',  '0',  '0');";
			$resultQueryUser = mysqli_query($link, $queryInsertUser);


			$kamialioq = "INSERT INTO subscriber (username, domain, password) VALUES ('$phone_login','goautodial.com','$phone_pass');";
			$resultkam = mysqli_query($linkgokam, $kamialioq);


			$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: Please Check your details and try again");
		}

		
		} else {
			$apiresults = array("result" => "Error: User already exist.");
		}
		} else {
			$apiresults = array("result" => "Error: Invalid User group");
		}
			}
	    }}}	  
	}}}
    }
?>
