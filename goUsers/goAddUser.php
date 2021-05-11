<?php
/**
 * @file 		goAddUser.php
 * @brief 		API to add new user 
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
    
    include_once ("goAPI.php");
	include_once ("../licensed-conf.php");
	
    // POST or GET Variables
	$orig_user 	= (isset($_REQUEST['user']) ? $astDB->escape($_REQUEST['user']) : "agent001");
	$pass 		= $astDB->escape($_REQUEST['pass']);
	$orig_full_name = (isset($_REQUEST['full_name']) ? $astDB->escape($_REQUEST['full_name']) : "Agent 001");
	$phone_login 	= $astDB->escape($_REQUEST['phone_login']);
	$phone_pass 	= $pass;
	$user_group 	= $astDB->escape($_REQUEST['user_group']);
	$active 	= $astDB->escape(strtoupper($_REQUEST['active']));		
	$email 		= $astDB->escape($_REQUEST['email']);
	$avatar 	= (isset($_REQUEST['avatar']) ? $astDB->escape($_REQUEST['avatar']) : NULL);
	$seats 		= (isset($_REQUEST['seats']) ? $astDB->escape($_REQUEST['seats']) : 1);
	$server_ip 	= (isset($_REQUEST['server_ip']) ? $astDB->escape($_REQUEST['server_ip']) : NULL);
	$defActive 	= array("Y", "N");

    // Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $orig_user)) {
		$err_msg 										= error_handle("41004", "user");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)) {
		$err_msg 										= error_handle("41004", "pass");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $orig_full_name)) {
		$err_msg 										= error_handle("41004", "full_name");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
	} elseif (empty($orig_user)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
    } elseif (empty($pass)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
    } elseif (empty($orig_full_name)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
    } elseif (empty($user_group)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
    } elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 										= error_handle("41006", "active");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		

		//$usergroup										= $fresults["user_group"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {
			// check if user_group is valid
			$fresults									= $astDB
				->where("user_group", $user_group)
				->getOne("vicidial_user_groups", "user_group");
			
			$usergroup									= $astDB->getRowCount();
			
			if ($usergroup > 0) {
				// Check License Seats //
				$num_users 								= $astDB
					->where("user", DEFAULT_USERS, "NOT IN") //DEFAULT_USERS = an array that is static, can be defined in goAPI.php
					->where("user_level", 4, "!=")
					->getValue("vicidial_users", "count(*)", null);
					
				// Check if DB Licensed Seats Exists //
				$fetch_license 							= $goDB
					->where("setting", "GO_licensed_seats")
					->getOne("settings");
						
				if ($goDB->count > 0) { 
					$licensedSeats 						= $fetch_license['value']; 
				} else { 
					$licensedSeats 						= $config["licensedSeats"]; 
				}
				
				$error_count 							= 0;
				$checker 								= 0;
				
				if ($num_users <= $licensedSeats || $licensedSeats == "0") {
					$rpasshash 							= $astDB->getOne("system_settings");

					$pass_hash_enabled 					= $rpasshash['pass_hash_enabled'];
					$pass_cost 							= $rpasshash['pass_cost'];
					$pass_key 							= $rpasshash['pass_key'];
					$get_last 							= preg_replace("/[^0-9]/","", $orig_user);
					$last_num_user 						= intval($get_last);				
					$get_last2 							= preg_replace("/[^0-9]/","", $orig_full_name);
					$last_num_name 						= intval($get_last2);				
					$arr_user 							= array();
					$add_num 							= 0;
					
					for ($i=0;$i < $seats;$i++) {
						$iterate_number1 				= $last_num_user + $add_num;
						$iterate_number2 				= $last_num_name + $add_num;
						
						if ($iterate_number1 > 0) {
							$user 						= str_replace ($last_num_user,$iterate_number1,$orig_user);
						} else {
							$user 						= $orig_user;
							
							if  ($last_num_user === 0 && $seats > 0) {
								$orig_user 				= $orig_user."1";
								$last_num_user 			= 1;
							}
						}
						
						if ($iterate_number2 > 0) {
							$full_name 					= str_replace ($last_num_name,$iterate_number2,$orig_full_name);
						} else {
							$full_name 					= $orig_full_name;
							
							if ($last_num_name === 0 && $seats > 0) {
								$orig_full_name 		= $orig_full_name."1";
								$last_num_name 			= 1;
							}
						}
						
						$phone_login 					= $phone_login + $add_num;				
						$add_num 						= $add_num + 1;

						if (!empty($location)) {
							$result_location 			= go_check_location ($location, $user_group);
							
							if ($result_location < 1) {
								$err_msg 				= error_handle("41006", "location. User group does not exist in the location selected.");
								
								$apiresults 			= array(
									"code" 					=> "41006", 
									"result" 				=> $err_msg
								);
								
								$location 				= "";
							}
						} else {
							$location 					= "";
						}
						
						// check if existing user
						$astDB->where("user", $user);
						$astDB->getOne("vicidial_users", "user") ;
						
						if ($astDB->count <= 0) {
                            if (!isset($server_ip)) {
                                $rServerIP 					= $astDB->getOne("servers", "server_ip");
                                $server_ip 					= $rServerIP['server_ip'];
                            }
							
							// check group_level
							$group_level 				= $goDB
								->where("user_group", $user_group)
								->getOne("user_access_group", "group_level");
								
							if ($goDB->count > 0) { 
								$user_level 			= $group_level['group_level']; 
							} else { 
								$user_level 			= 1;
							}
				
							
							/*if (strtolower($user_group) == "admin") {
								$user_level 				= "9";
								$phone_pass 				= "";
								$phone_login 				= "";
								$agentcall_manual 			= "1";
								$agentonly_callbacks 		= "1";
							} else {
								$user_level 				= "1";
								$agentcall_manual 			= "1";
								$agentonly_callbacks 		= "1";
							}*/
						
							# generate random phone login
							$x 							= 0;
							$y 							= 0;
							
							while ($x == $y) {
								$random_digit 			= mt_rand(1000000000, 9999999999);
								$astDB->where("phone_login", $random_digit);
								$astDB->getOne("vicidial_users", "phone_login");
								
								if ($astDB->count <= 0) {
									$y 					= 1;
									$phone_login 		= $random_digit;
								}
							}
							
							$pass_hash 					= "";
							
							if ($pass_hash_enabled > 0) {
								$pass_hash 				= encrypt_passwd($pass, $pass_cost, $pass_key);
								$password 				= "";
							} else {
								$password				= $pass;
							}
							
							
							$dataUser = array(
								"user" 				=> $user,
								"pass" 				=> $password,
								"user_group" 			=> $user_group,
								"full_name" 			=> $full_name,
								"user_level" 			=> $user_level,
								"email" 			=> $email,
								"phone_login" 			=> $phone_login,
								"phone_pass" 			=> $password,
								"agentonly_callbacks" 		=> "1",
								"agentcall_manual" 		=> "1",
								"active" 			=> $active,
								"vdc_agent_api_access" 		=> "1",
								"pass_hash" 			=> $pass_hash,
								"agent_choose_ingroups" 	=> "1",
								"vicidial_recording" 		=> "1",
								"vicidial_transfers" 		=> "1",
								"closer_default_blended" 	=> "1",
								"scheduled_callbacks" 		=> "1"
							);
							
							$q_insertUser 				= $astDB->insert('vicidial_users', $dataUser); // insert record in asterisk.vicidial_users
							$log_id 				= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User: $user", $log_group, $astDB->getLastQuery());
							
							$dataPhones = array(
								"extension" 			=> $phone_login,
								"dialplan_number" 		=> "9999" . $phone_login,
								"voicemail_id" 			=> $phone_login,
								"phone_ip" 			=> "",
								"computer_ip" 			=> "",
								"server_ip" 			=> $server_ip,
								"login" 			=> $phone_login,
								"pass" 				=> $password,
								"status" 			=> "ACTIVE",
								"active" 			=> $active,
								"phone_type" 			=> "",
								"fullname" 			=> $full_name,
								"company" 			=> $user_group,
								"picture" 			=> "",
								"protocol" 			=> "EXTERNAL",
								"local_gmt" 			=> "-5",
								"outbound_cid" 			=> "0000000000",
								"template_id" 			=> "--NONE--",
								//"conf_override" => $conf_override,
								"user_group" 			=> $user_group,
								"conf_secret" 			=> $password,
								"messages" 			=> "0",
								"old_messages" 			=> "0"
							);
							
							$astDB->insert('phones', $dataPhones); // insert record in goautodial.users
							
							$log_id 				= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User: $user", $log_group, $astDB->getLastQuery());
							
							$astDB->where("user", $user);
							$query = $astDB->getOne("vicidial_users", "user_id");

							$userid = $query["user_id"];
											
							if  ($active == "N") { 
								$goactive = 0; 
							} else { 
								$goactive = 1; 
							}
							
							$datago = array(
								"userid" 			=> $userid,
								"name" 				=> $user,
								"fullname" 			=> $full_name,
								"avatar" 			=> $avatar,
								"role" 				=> $user_level,
								"status" 			=> $goactive,
								"user_group" 			=> $user_group,
								"phone" 			=> $phone_login,
								"email"				=> $email
								//"location_id" => $location
							);
							
							$goDB->insert('users', $datago); // insert record in goautodial.users
							
							// Admin logs
							$log_id	= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User: $user", $log_group, $goDB->getLastQuery());
								
							$astDB->where("user", $user);
							$astDB->getOne("vicidial_users", "user");
							
							if ($astDB->count > 0) {
								$goDB->where("setting", "GO_agent_wss_sip");
								$querygo = $goDB->getOne("settings", "value");
								$realm = $querygo['value'];
								
								if  ($pass_hash_enabled > 0) {
									$ha1 = md5 ("{$phone_login}:{$realm}:{$phone_pass}");
									$ha1b = md5 ("{$phone_login}@{$realm}:{$realm}:{$phone_pass}");
									$phone_pass = '';
								}

								$goDB->where("setting", "GO_agent_domain");
								$rowd = $goDB->getOne("settings", "value");

								$domain = (!is_null($rowd['value']) || $rowd['value'] !== '') ? $rowd['value'] : 'goautodial.com';
							
								$datakam = array(
									"username" 		=> $phone_login,
									"domain" 		=> $domain,
									"password" 		=> $phone_pass,
									"ha1" 			=> $ha1,
									"ha1b" 			=> $ha1b
								);							
							
								$kamDB->insert('subscriber', $datakam);
								$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New User: $user", $log_group, $kamDB->getLastQuery());
								
								$return_user = $userid;
								array_push ($arr_user, $return_user);							
							} else {
								$err_msg = error_handle("41004", "user");
								$apiresults = array(
									"code" 			=> "41004", 
									"result" 		=> $err_msg
								);
							}								
						} else {				
							$error_count = 1;
							$i = $i - 1;
						}
					}
					
					if ($error_count == 0) {
						$apiresults = array(
							"result" => "success", 
							"user created" => $arr_user
						);
					} elseif ($error_count == 1) {
						$err_msg = error_handle("10113");
						$apiresults = array(
							"code" 					=> "10113", 
							"result" 				=> $err_msg
						);
					} elseif ($error_count == 2) {
						$err_msg = error_handle("41004", "user_group");
						$apiresults = array(
							"code" 					=> "41004", 
							"result" 				=> $err_msg
						);
					}
				} else {
					$err_msg = error_handle("10004", "seats. Reached Maximum Licensed Seats!");
					$apiresults = array(
						"code" 						=> "10004", 
						"result" 					=> $err_msg
					);
				}			
			} else {
				$err_msg = error_handle("41004", "user_group");
				$apiresults = array(
					"code" 							=> "41004", 
					"result" 						=> $err_msg
				);
			}
		} else {
			$err_msg = error_handle("10001");
			$apiresults = array(
				"code" 								=> "10001", 
				"result" 							=> $err_msg
			);		
		}
	}
    
?>
