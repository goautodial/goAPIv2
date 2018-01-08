<?php

   /*************************************************
   ### Name: goAddUser.php                        ###
   ### Description: API to add new user           ###
   ### Version: 0.9                               ###
   ### Copyright: GOAutoDial Ltd. (c) 2011-2015   ###
   ### Written by: Jeremiah Sebastian Samatra     ###
   ###	Updated by: Alexander Jim Abenoja         ###
   ### License: AGPLv2                            ###
   *************************************************/
    
    include_once ("goAPI.php");
	include_once ("../licensed-conf.php");
	
    // POST or GET Variables
        $orig_user = $_REQUEST['user'];
        $pass = $_REQUEST['pass'];
        $orig_full_name = $_REQUEST['full_name'];
        $phone_login = $_REQUEST['phone_login'];
        $phone_pass = $pass;
        $user_group = $_REQUEST['user_group'];
        $active = strtoupper($_REQUEST['active']);
        $location = $_REQUEST['location_id'];
		
	if(isset($_REQUEST['seats']))
            $seats = $_REQUEST['seats'];
	else
	    $seats = 1;
		
	$avatar = $_REQUEST['avatar'];
	$goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
		
	$log_user = $_REQUEST['log_user'];
	$log_group = $_REQUEST['log_group'];

    // Default values 
        $defActive = array("Y","N");

    // Error Checking
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $orig_user)){
		$err_msg = error_handle("41004", "user");
		$apiresults = array("code" => "41004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in user");
	} else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
		$err_msg = error_handle("41004", "pass");
		$apiresults = array("code" => "41004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in password");
	} else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $orig_full_name)){
		$err_msg = error_handle("41004", "full_name");
		$apiresults = array("code" => "41004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in full_name");
	} else {
        if($orig_user == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
            //$apiresults = array("result" => "Error: Set a value for User.");
    } else {
	    if($pass == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for password.");
    } else {
    if($orig_full_name == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for Full name.");
    } else {
    if($user_group == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for User Group.");
    } else {
	if(!in_array($active,$defActive) && $active != null) {
		$err_msg = error_handle("41006", "active");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} else {
		// Check License Seats //		
		$astDB->where("user", $default_users, "NOT IN");//$default_users = an array that is static, can be defined in goAPI.php
		$astDB->where("user_level", 4, "IS NOT");
		$num_users = $astDB->getValue("vicidial_users", "count(*)", null);
		//$license_query = mysqli_query($link, "SELECT user FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI') AND user_level != '4' ORDER BY user ASC");
			
		// Check if DB Licensed Seats Exists //
		$goDB->where("setting", "GO_licensed_seats");
		$fetch_license = $goDB->getOne("settings");
		//$check_db_license = "SELECT * FROM settings WHERE setting = 'GO_licensed_seats' LIMIT 1;";	
				
				if($goDB->count > 0){
					$licensedSeats = $fetch_license['value'];
				}else{
					$licensedSeats = $config["licensedSeats"];
				}
			
				$error_count = 0;
				$checker = 0;
				if($num_users <= $licensedSeats || $licensedSeats == "0"){
					$rpasshash = $astDB->getOne("system_settings");
					//$queryPassHash = "SELECT pass_hash_enabled from system_settings";
					$pass_hash_enabled = $rpasshash['pass_hash_enabled'];

					$get_last = preg_replace("/[^0-9]/","", $orig_user);
					$last_num_user = intval($get_last);
					
					$get_last2 = preg_replace("/[^0-9]/","", $orig_full_name);
					$last_num_name = intval($get_last2);
					
					$arr_user = array();
					//$test = array();
					$add_num = 0;
					for($i=0;$i < $seats;$i++){
						$iterate_number1 = $last_num_user + $add_num;
						$iterate_number2 = $last_num_name + $add_num;
						
						if($iterate_number1 > 0){
							$user = str_replace($last_num_user,$iterate_number1,$orig_user);
						}else{
							$user = $orig_user;
							if($last_num_user === 0 && $seats > 0){
								$orig_user = $orig_user."1";
								$last_num_user = 1;
							}
						}
						
						//array_push($test, $iterate_number1."=".$orig_user);
						
						if($iterate_number2 > 0)
						$full_name = str_replace($last_num_name,$iterate_number2,$orig_full_name);
						else{
							$full_name = $orig_full_name;
							if($last_num_name === 0 && $seats > 0){
								$orig_full_name = $orig_full_name.'1';
								$last_num_name = 1;
							}
						}
						
						$phone_login = $phone_login + $add_num;
						
						$add_num = $add_num + 1;
						
						$groupId = go_get_groupid($goUser);
						if (!checkIfTenant($groupId)) {
							$ulUser = "AND user= ?";
							$arrUserCheck = array('VDAD', 'VDCL', 4, $phone_login, $phone_login, $user);

							$ulug = "WHERE user_group=?";
							$arrlug = array($user_group);
						} else {
							$ulUser = "AND user=? AND user_group=?";
							$arrUserCheck = array('VDAD', 'VDCL', 4, $phone_login, $phone_login, $user, $groupId);

							$ulug = "WHERE user_group=? AND user_group=?";
							$arrlug = array($user_group, $groupId);
						}

						if(!empty($location)){
							$result_location = go_check_location($location, $user_group);
							
							if($result_location < 1){
								$err_msg = error_handle("41006", "location. User group does not exist in the location selected.");
								$apiresults = array("code" => "41006", "result" => $err_msg);
								$location = "";
							}
						}else{
							$location = "";
						}
						
						
						$query = $astDB->rawQueryValue("SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;", $arrlug);
						
						if($query->count > 0) {

							$queryUserCheck = $ast->rawQuery("SELECT user, full_name, user_level, user_group, phone_login, active FROM vicidial_users WHERE user NOT IN (?,?) AND user_level != ? AND ? = (SELECT phone_login FROM vicidial_users WHERE phone_login = ?)  $ulUser ORDER BY user ASC LIMIT 1;", $arrUserCheck);
							//$rsltvCheck = mysqli_query($link, $queryUserCheck);
							//$countCheckResult = mysqli_num_rows($rsltvCheck);
							
							if($queryUserCheck->count <= 0) {
								$rServerIP = $astDB->getOne("servers");
								//$querygetserverip = "select server_ip from servers;";
								//$rsltserverip = mysqli_query($link, $querygetserverip);
								//$rServerIP = mysqli_fetch_array($rsltserverip, MYSQLI_ASSOC);
								$server_ip = $rServerIP['server_ip'];
							
								if(strtolower($user_group) == "admin"){
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
							
							# generate random phone login
								$x = 0;
								$y = 0;
								while($x == $y){
									$random_digit = mt_rand(1000000000, 9999999999);
									$astDB->where("phone_login", $random_digit);
									$check_existing_phonelogins_query = $astDB->getValue("vicidial_users", "phone_login");
									//$check_existing_phonelogins_query = "SELECT phone_login FROM vicidial_users WHERE phone_login = '$random_digit';";
									//$check_existing_phonelogins_exec_query = mysqli_query($link, $check_existing_phonelogins_query);
									
									if($check_existing_phonelogins_query->count == true){
										$y = 1;
										$phone_login = $random_digit;
									}
								}
							
								$pass_hash = '';
								if ($pass_hash_enabled > 0) {
									$cwd = $_SERVER['DOCUMENT_ROOT'];
									$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
									$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
									//$pass = '';
								}
								$data = array(
									"user" => $user,
									"pass" => $pass,
									"user_group" => $user_group,
									"full_name" => $full_name,
									"user_level" => $user_level,
									"phone_login" => $phone_login,
									"phone_pass" => $phone_pass,
									"agentonly_callbacks" => $agentonly_callbacks,
									"agentcall_manual" => $agentcall_manual,
									"active" => $active,
									"vdc_agent_api_access" => 1,
									"pass_hash" => $pass_hash,
									"agent_choose_ingroups" => 1,
									"vicidial_recording" => 1,
									"vicidial_transfers" => 1,
									"closer_default_blended" => 1,
									"scheduled_callbacks" => 1
								);
								$queryUserAdd = $astDB->insert('vicidial_users', $data); // insert record in asterisk.vicidial_users

								//$queryUserAdd = "INSERT INTO  vicidial_users (user, pass, user_group, full_name, user_level, phone_login, phone_pass, agentonly_callbacks, agentcall_manual, active, vdc_agent_api_access,pass_hash, agent_choose_ingroups, vicidial_recording, vicidial_transfers, closer_default_blended, scheduled_callbacks) VALUES ('$user', '$pass', '$user_group', '$full_name', '$user_level', '$phone_login', '$phone_pass', '$agentonly_callbacks', '$agentcall_manual', '$active', '1', '$pass_hash', '1', '1', '1', '1', '1');";
								$astDB->where("user", $user);
								$rUserID = $astDB->getOne("vicidial_users", "user_id");
								//$queryUserID = "SELECT user_id from vicidial_users WHERE user='$user'";
								$userid = $rUserID['user_id'];
								
								if ($active == "N") {
                                    $goactive = "0";
								} else {
                                    $goactive = "1";
								}
								
								$data = array(
									"userid" => $userid,
									"name" => $user,
									"fullname" => $full_name,
									"avatar" => $avatar,
									"role" => $user_level,
									"status" => $goactive,
									"user_group" => $user_group,
									"phone" => $phone_login,
									"location_id" => $location
								);
								$queryUserAddGo = $goDB->insert('users', $data); // insert record in goautodial.users
								$SQL_queryUserAddGo = "INSERT INTO users (userid, name, fullname, avatar, role, status, user_group, phone, location_id) VALUES ('$userid', '$user', '$full_name', '$avatar', '$user_level', '$goactive', '$user_group', '$phone_login', '$location')";
								//$resultQueryAddUserGo = mysqli_query($linkgo, $queryUserAddGo);
								
							// Admin logs
								//$SQLdate = date("Y-m-d H:i:s");
								//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New User $user','INSERT INTO vicidial_users (user,pass,full_name,phone_login,phone_pass,user_group,active) VALUES ($user,$pass,$full_name,$phone_login,$phone_pass,$user_group,$active)');";
								//$rsltvLog = mysqli_query($linkgo, $queryLog);
								$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New User: $user", $log_group, $SQL_queryUserAddGo);
								
								// Checkagain
								if($queryUserAddGo && $queryUserAdd) {
									$goDB->where("setting", "GO_agent_wss_sip");
									$queryg = $goDB->getOne("settings", "value");
									//$queryg = "SELECT value FROM settings WHERE setting='GO_agent_wss_sip';";
									$realm = $queryg['value'];
									
									$kamha1fields = '';
									$kamha1values = '';
									if ($pass_hash_enabled > 0) {
										$ha1 = md5("{$phone_login}:{$realm}:{$phone_pass}");
										$ha1b = md5("{$phone_login}@{$realm}:{$realm}:{$phone_pass}");
										$kamha1fields = ", ha1, ha1b";
										$kamha1values = ", '{$ha1}', '{$ha1b}'";
										$phone_pass = '';
									}

									$goDB->where("setting", "GO_agent_domain");
									$rowd = $goDB->getOne("settings", "value");
									//$queryd = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
									
									$domain = (!is_null($rowd['value']) || $rowd['value'] !== '') ? $rowd['value'] : 'goautodial.com';
									
									$data = array(
										"extension" => $phone_login,
										"dialplan_number" => "9999"{$phone_login},
										"voicemail_id" => $phone_login,
										"phone_ip" => "",
										"computer_ip" => "",
										"server_ip" => $server_ip,
										"login" => $phone_login,
										"pass" => $phone_pass,
										"status" => "ACTIVE",
										"active" => $active,
										"phone_type" => "",
										"fullname" => $full_name,
										"company" => $user_group,
										"picture" => "",
										"protocol" => "EXTERNAL",
										"local_gmt" => -5,
										"outbound_cid" => "0000000000",
										"template_id" => "--NONE--",
										"conf_override" => $conf_override,
										"user_group" => $user_group,
										"conf_secret" => $phone_pass,
										"messages" => 0,
										"old_messages" => 0
									);
									$queryInsertUser = $goDB->insert('users', $data); // insert record in goautodial.users
									//$queryInsertUser = "INSERT INTO `phones` (`extension`,  `dialplan_number`,  `voicemail_id`,  `phone_ip`,  `computer_ip`,  `server_ip`,  `login`,  `pass`,  `status`,  `active`,  `phone_type`,  `fullname`,  `company`,  `picture`,  `protocol`,  `local_gmt`,  `outbound_cid`,  `template_id`,  `conf_override`,  `user_group`,  `conf_secret`,  `messages`,  `old_messages`) VALUES ('$phone_login',  '9999$phone_login',  '$phone_login',  '',  '', '$server_ip',  '$phone_login',  '$phone_pass',  'ACTIVE',  '$active',  '',  '$full_name',  '$user_group',  '',  'EXTERNAL',  '-5',  '0000000000',  '--NONE--',  '$conf_override',  '$user_group',  '$phone_pass',  '0',  '0');";
									
									$data = array(
										"username" => $phone_login,
										"domain" => "9999".$domain,
										"password{$kamha1fields}" => $phone_pass{$kamha1values}
									);
									$queryInsertUser = $goDB->insert('subscriber', $data); // insert record in kamilio.subscriber
									//$kamailioq = "INSERT INTO subscriber (username, domain, password{$kamha1fields}) VALUES ('$phone_login','$domain','$phone_pass'{$kamha1values});";
									
									//$return_user = $user." (".$userid.")";
									$return_user = $userid;
									array_push($arr_user, $return_user);
									
								} else {
									$err_msg = error_handle("41004", "user");
									$apiresults = array("code" => "41004", "result" => $err_msg);
									//$apiresults = array("result" => "Error: A problem occured while adding a user. Please Contact the System Administrator.");
								}
								
							} else {
								$error_count = 1;
								$i = $i - 1;
							}
						} else {
							$error_count = 2;
						}
					}
					
					if($error_count == 0){
						$apiresults = array("result" => "success", "user created" => $arr_user);
					}elseif($error_count == 1){
						$err_msg = error_handle("10113");
						$apiresults = array("code" => "10113", "result" => $err_msg);
						//$apiresults = array("result" => "Error: User already exist.");
					}else{
						$err_msg = error_handle("41004", "user_group");
						$apiresults = array("code" => "41004", "result" => $err_msg);
						//$apiresults = array("result" => "Error: Invalid User group");
					}
					
					
				}else{
					$err_msg = error_handle("10004", "seats. Reached Maximum Licensed Seats!");
					$apiresults = array("code" => "10004", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Reached Maximum Licensed Seats!");
				}
			}
	}}}	  
	}}}
    }
?>
