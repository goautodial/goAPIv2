<?php
   ///////////////////////////////////////////////////////
   /// Name: goEditUser.php 		///
   /// Description: API to edit specific user 		///
   /// Version: 0.9 		///
   /// Copyright: GOAutoDial Ltd. (c) 2011-2015 		///
   /// Written by: Jeremiah Sebastian V. Samatra 		///
   /// License: AGPLv2 		///
   ///////////////////////////////////////////////////////
    
    include_once ("../goFunctions.php");

    // Check file is existed
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
        $conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
    } elseif (file_exists("/etc/goautodial.conf")) {
        $conf_path = "/etc/goautodial.conf";
    } else {
        $apiresults = array("result" => "Error: File goautodial.conf not found.");
    }
 
    // POST or GET Variables		
    $userid = mysqli_real_escape_string($link, $_REQUEST['user_id']);
    $user = mysqli_real_escape_string($link, $_REQUEST['user']);
    $pass = mysqli_real_escape_string($link, $_REQUEST['pass']);
    $full_name = mysqli_real_escape_string($link, $_REQUEST['full_name']);
    $phone_login = mysqli_real_escape_string($link, $_REQUEST['phone_login']);
    $phone_pass = $pass;
    $user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
    $email = mysqli_real_escape_string($link, $_REQUEST['email']);
    $active = strtoupper($_REQUEST['active']);
    $hotkeys_active = $_REQUEST['hotkeys_active'];
    $user_level = $_REQUEST['user_level'];
    $modify_same_user_level = strtoupper($_REQUEST['modify_same_user_level']);
    $ip_address = $_REQUEST['hostname'];
    $goUser = $_REQUEST['goUser'];
    $voicemail = $_REQUEST['voicemail'];
    $vdc_agent_api_access = $_REQUEST['vdc_agent_api_access'];
    $agent_choose_ingroups = $_REQUEST['agent_choose_ingroups'];
    $vicidial_recording_override = $_REQUEST['vicidial_recording_override'];
    $vicidial_transfers = $_REQUEST['vicidial_transfers'];
    $closer_default_blended = $_REQUEST['closer_default_blended'];
    $agentcall_manual = $_REQUEST['agentcall_manual'];
    $scheduled_callbacks = $_REQUEST['scheduled_callbacks'];
    $agentonly_callbacks = $_REQUEST['agentonly_callbacks'];
    $agent_lead_search_override = $_REQUEST['agent_lead_search_override'];
    $avatar = $_REQUEST['avatar'];
    
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	
    // Default Values
    $defActive = array("Y","N");
    $defmodify_same_user_level = array("Y","N");	

    // Error Checking
    if(empty($user) && empty($userid)) {
		$err_msg = error_handle("40002");
		$apiresults = array("code" => "40002", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for User ID.");
    } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user) || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $userid)){
			$err_msg = error_handle("41006", "user_id or user");
			$apiresults = array("code" => "41006", "result" => $err_msg);
            //$apiresults = array("result" => "Error: Special characters found in user");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
				$err_msg = error_handle("41006", "pass");
				$apiresults = array("code" => "41006", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Special characters found in password");
			} else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $full_name)){
					$err_msg = error_handle("41006", "full_name");
					$apiresults = array("code" => "41006", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Special characters found in full_name");
				} else {
					if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_login)){
						$err_msg = error_handle("41002", "phone_login");
						$apiresults = array("code" => "41002", "result" => $err_msg);
						//$apiresults = array("result" => "Error: Special characters found in phone_login");
					} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_pass)){
							$err_msg = error_handle("41004", "phone_pass");
							$apiresults = array("code" => "41004", "result" => $err_msg);
							//$apiresults = array("result" => "Error: Special characters found in phone_pass");
						} else {
							if(!in_array($active,$defActive) && $active != null) {
								$err_msg = error_handle("41006", "active");
								$apiresults = array("code" => "41006", "result" => $err_msg);
								//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
							} else {
								if(!in_array($modify_same_user_level,$defmodify_same_user_level) && $modify_same_user_level != null) {
									$err_msg = error_handle("41006", "modify_same_user_level");
									$apiresults = array("code" => "41006", "result" => $err_msg);
									//$apiresults = array("result" => "Error: Default value for modify_same_user_level is Y or N only.");
								} else {
									if($user_level < 1 && $user_level!=null || $user_level > 9 && $user_level!= null) {
										$err_msg = error_handle("41002", "user_level");
										$apiresults = array("code" => "41002", "result" => $err_msg);
										//$apiresults = array("result" => "Error: User Level Value should be in between 1 and 9");
									} else {
										if($VARSERVTYPE == "gofree" && $hotkeys_active != null) {
											$err_msg = error_handle("10004", "hotkeys_active. Hotkeys is disabled");
											$apiresults = array("code" => "10004", "result" => $err_msg);
											//$apiresults = array("result" => "Error: hotkeys is disabled");
										} else {
											if($modify_same_user_level != null) {
												$err_msg = error_handle("10004", "modify_same_user_level. modify_same_user_level is disabled");
												$apiresults = array("code" => "10004", "result" => $err_msg);
												//$apiresults = array("result" => "Error: modify_same_user_level is disabled");
											} elseif(empty($session_user)) {
												$err_msg = error_handle("40002");
												$apiresults = array("code" => "40002", "result" => $err_msg);
											}else{
												$groupId = go_get_groupid($session_user);
												if (!checkIfTenant($groupId)) {
													$ul = "WHERE user_group='$user_group'";
													
													if($userid != NULL){
														$ulUser = "AND user_id='$userid'";
													}else{
														$ulUser = "AND user='$user'";
													}
												} else {
													$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
													
													if($userid != NULL){
														$ulUser = "AND user_id='$userid' AND user_group='$groupId'";
													}else{
														$ulUser = "AND user='$user' AND user_group='$groupId'";
													}
												}
												
												if($voicemail != null){
													$voicemail_query = ", `voicemail_id` = '$voicemail'";
												}else{
													$voicemail_query = "";
												}
												
												$queryUserCheck = "
																SELECT * 
																FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') 
																AND user_level != '4' $ulUser 
																ORDER BY user 
																ASC LIMIT 1
																";
																
												$rsltvCheck = mysqli_query($link, $queryUserCheck);
												$countCheckResult = mysqli_num_rows($rsltvCheck);
										
												if($countCheckResult > 0) {
													while($fresults = mysqli_fetch_array($rsltvCheck, MYSQLI_ASSOC)){
														$dataUser = $fresults['user'];
														$data_phone_login = $fresults['phone_login'];
														$dataUserLevel = $fresults['user_level'];
														$dataFullname = $fresults['full_name'];
														$dataUserGroup = $fresults['user_group'];
														$dataActive = $fresults['active'];
														$dataEmail = $fresults['email'];
														$dataHotkeys = $fresults['hotkeys_active'];
														$data_modify_same_user_level = $fresults['modify_same_user_level'];
														$dataVoicemail = $fresults['voicemail'];
														$data_vdc_agent_api_access = $fresults['vdc_agent_api_access'];
														$data_agent_choose_ingroups = $fresults['agent_choose_ingroups'];
														$data_vicidial_recording_override = $fresults['vicidial_recording_override'];
														$data_vicidial_transfers = $fresults['vicidial_transfers'];
														$data_closer_default_blended = $fresults['closer_default_blended'];
														$data_agentcall_manual = $fresults['agentcall_manual'];
														$data_scheduled_callbacks = $fresults['scheduled_callbacks'];
														$data_agentonly_callbacks = $fresults['agentonly_callbacks'];
														$data_agent_lead_search_override = $fresults['agent_lead_search_override'];
													}
													
													if(empty($phone_login))
														$phone_login = $data_phone_login;
													if(empty($user_level))
														$user_level = $dataUserLevel;
													if(empty($full_name))
														$full_name = $dataFullname;
													if(empty($user_group))
														$user_group = $dataUserGroup;
													if(empty($active))
														$active = $dataActive;
													if(empty($email))
														$email = $dataEmail;
													if(empty($hotkeys_active))
														$hotkeys_active = $dataHotkeys;
													if(empty($modify_same_user_level))
														$modify_same_user_level = $data_modify_same_user_level;
													if(empty($voicemail))
														$voicemail = $dataVoicemail;
													if(empty($vdc_agent_api_access))
														$vdc_agent_api_access = $data_vdc_agent_api_access;
													if(empty($agent_choose_ingroups))
														$agent_choose_ingroups = $data_agent_choose_ingroups;
													if(empty($vicidial_recording_override))
														$vicidial_recording_override = $data_vicidial_recording_override;
													if(empty($vicidial_transfers))
														$vicidial_transfers = $data_vicidial_transfers;
													if(empty($closer_default_blended))
														$closer_default_blended = $data_closer_default_blended;
													if(empty($agentcall_manual))
														$agentcall_manual = $data_agentcall_manual;
													if(empty($scheduled_callbacks))
														$scheduled_callbacks = $data_scheduled_callbacks;
													if(empty($agentonly_callbacks))
														$agentonly_callbacks = $data_agentonly_callbacks;
													if(empty($agent_lead_search_override))
														$agent_lead_search_override = $data_agent_lead_search_override;
														
													if( $modify_same_user_level == "Y") {
														$modify_same_user_level = 0;
													} else {
														$modify_same_user_level = 1;
													}
													
													//# Check User Group if valid
													if($user_group != null){
														$query = "SELECT user_group FROM vicidial_user_groups WHERE user_group = '$user_group' ORDER BY user_group LIMIT 1;";
														$rsltv = mysqli_query($link, $query);
														$countResult = mysqli_num_rows($rsltv);
													}else{
														$err_msg = error_handle("41004", "user_group. Doesn't exist");
														$apiresults = array("code" => "41004", "result" => $err_msg);
													}
													
													if($countResult <= 0 && $user_group!=null) {
														$err_msg = error_handle("41004", "user_group. Doesn't exist");
														$apiresults = array("code" => "41004", "result" => $err_msg);
													} else {
														// Password Encryption
														$cwd = $_SERVER['DOCUMENT_ROOT'];
														$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
														$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
														
														if($pass != NULL){
															$query_passhash = "select pass_hash_enabled from system_settings";
															$exec_query = mysqli_query($link, $query_passhash);
															$fetch_pass_hash_enabled = mysqli_fetch_array($exec_query, MYSQLI_ASSOC);
															
															if($fetch_pass_hash_enabled['pass_hash_enabled'] == "1"){
																$pass_query = "`pass_hash` = '$pass_hash', `pass` = '', `phone_pass` = '$pass_hash', ";
																$phonePassQuery = "`pass` = ''";
																
																$queryg = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
																$rsltg = mysqli_query($linkgo, $queryg);
																$rowg = mysqli_fetch_array($rsltg, MYSQLI_ASSOC);
																$realm = (!is_null($rowg['value']) || $rowg['value'] !== '') ? $rowg['value'] : 'goautodial.com';
																
																$ha1 = md5("{$phone_login}:{$realm}:{$phone_pass}");
																$ha1b = md5("{$phone_login}@{$realm}:{$realm}:{$phone_pass}");
																$kamPassQuery = "SET `password` = '', `ha1` = '$ha1', `ha1b` = '$ha1b'";
															}else{
																$pass_query = "`pass_hash` = '$pass_hash', `pass` = '$pass', `phone_pass` = '$phone_pass', ";
																$phonePassQuery = "`pass` = '$pass'";
																$kamPassQuery = "SET `password` = '$pass', `ha1` = '', `ha1b` = ''";
															}
																
															$queryUpdatePhones = "UPDATE `phones` SET `conf_secret` = '$pass', $phonePassQuery WHERE `extension` = '$phone_login'";
															$resultQueryUser = mysqli_query($link, $queryUpdatePhones);
															
															$kamailioq = "UPDATE `subscriber` $kamPassQuery WHERE `username` = '$phone_login'";
															$resultkam = mysqli_query($linkgokam, $kamailioq);
														}else{
															$pass_query = "";
														}
														
														if($phone_login != NULL){
															$phonelogin_query = "`phone_login` = '$phone_login', ";
														}else{
															$phonelogin_query = "";
														}
								
														if($userid != NULL){
															$queryUpdateUser = "UPDATE `vicidial_users`
																				SET $pass_query `full_name` = '$full_name',  $phonelogin_query  `user_group` = '$user_group',  `active` = '$active',
																					`hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level', `vdc_agent_api_access` = '$vdc_agent_api_access', 
																					`agent_choose_ingroups` = '$agent_choose_ingroups', `vicidial_recording_override` = '$vicidial_recording_override', 
																					`vicidial_transfers` = '$vicidial_transfers', `closer_default_blended` = '$closer_default_blended', `agentcall_manual` = '$agentcall_manual', 
																					`scheduled_callbacks` = '$scheduled_callbacks', `agentonly_callbacks` = '$agentonly_callbacks', 
																					`modify_same_user_level` = '$modify_same_user_level', `email` = '$email', `agent_lead_search_override` = '$agent_lead_search_override'  $voicemail_query 
																				WHERE `user_id` = '$userid'";
															
															$queryUserIDGo = "SELECT userid FROM users WHERE userid='$userid'";
															$resultQueryUserIDGo = mysqli_query($linkgo, $queryUserIDGo) or die(mysqli_error($linkgo));
															$rUserIDGo = mysqli_fetch_array($resultQueryUserIDGo, MYSQLI_ASSOC);
															$countResultGo = mysqli_num_rows($resultQueryUserIDGo);
															//$userIDGo = $rUserIDGo['userid'];
															
															if ($active == "N") {
																$goactive = "0";
															} else {
																$goactive = "1";
															}
															
															if ($countResultGo > 0){
																$queryUpdateUserGo = "UPDATE users 
																						SET `name` = '$dataUser',
																							`fullname` = '$full_name', 
																							`phone` = '$phone_login',
																							`email` = '$email',
																							`avatar` = '$avatar',
																							`user_group` = '$user_group',
																							`role` = '$user_level',
																							`status` = '$goactive'
																						WHERE userid = '$userid'";                                                    
															} else {
																$queryUpdateUserGo = "INSERT INTO users (userid, name, fullname, phone, email, avatar, user_group, role, status) 
																						VALUES ('$userid', '$dataUser', '$full_name', '$phone_login', '$email', '$avatar', '$user_group', '$user_level', '$goactive')";
															}
														}else{
															$queryUpdateUser = "UPDATE `vicidial_users` 
																				SET $pass_query `full_name` = '$full_name',  $phonelogin_query  `user_group` = '$user_group',  `active` = '$active',
																					`hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level', `vdc_agent_api_access` = '$vdc_agent_api_access', 
																					`agent_choose_ingroups` = '$agent_choose_ingroups', `vicidial_recording_override` = '$vicidial_recording_override', 
																					`vicidial_transfers` = '$vicidial_transfers', `closer_default_blended` = '$closer_default_blended', 
																					`agentcall_manual` = '$agentcall_manual', `scheduled_callbacks` = '$scheduled_callbacks', `agentonly_callbacks` = '$agentonly_callbacks', 
																					`modify_same_user_level` = '$modify_same_user_level', `email` = '$email', `agent_lead_search_override` = '$agent_lead_search_override'  $voicemail_query 
																				WHERE `user` = '$user'";
															
															$queryUserIDGo = "SELECT name from users WHERE name='$user'";
															$resultQueryUserIDGo = mysqli_query($linkgo, $queryUserIDGo) or die(mysqli_error($linkgo));
															$rUserIDGo = mysqli_fetch_array($resultQueryUserIDGo, MYSQLI_ASSOC);
															$countResultGo = mysqli_num_rows($resultQueryUserIDGo);
															//$userGo = $rUserIDGo['user'];
															
															if ($active == "N") {
																$goactive = "0";
															} else {
																$goactive = "1";
															}
															
															if ($countResultGo > 0){
																$queryUpdateUserGo = "UPDATE users 
																						SET `name` = '$dataUser',
																							`fullname` = '$full_name', 
																							`phone` = '$phone_login',
																							`email` = '$email',
																							`avatar` = '$avatar',
																							`user_group` = '$user_group',
																							`role` = '$user_level',
																							`status` = '$goactive'
																						WHERE userid = '$user'";
															} else {
																$queryUpdateUserGo = "INSERT INTO users (userid, name, fullname, phone, email, avatar, user_group, role, status) 
																					VALUES ('$userid', '$user', '$full_name', '$phone_login', '$email', '$avatar', '$user_group', '$user_level', '$goactive')";
															}
														}
														
														$resultQueryUser = mysqli_query($link, $queryUpdateUser) or die(mysqli_error($link));
														$resultQueryUserGo = mysqli_query($linkgo, $queryUpdateUserGo) or die(mysqli_error($linkgo));
														
														$queryJSIUpdate = "UPDATE justgovoip_sippy_info SET web_password='$phone_pass' where carrier_id='$user_group'";
														$resultQueryJSIUpdate = mysqli_query($link, $queryJSIUpdate) or die(mysqli_error($link));
														
														if ($userid != NULL) {
															$result = mysqli_query($link, "SELECT user FROM vicidial_users WHERE user_id='$userid';") or die(mysqli_error($link));
															$userInfo = mysqli_fetch_array($result, MYSQLI_ASSOC);
															$user = $userInfo['user'];
														}
														
														$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified User: $user", $log_group, $queryUpdateUser);
														
														if($resultQueryUser == false) {
															$err_msg = error_handle("10010");
															$apiresults = array("code" => "10010", "result" => $err_msg);
														} else {
															$apiresults = array("result" => "success");
														}
													}
												} else {
													$err_msg = error_handle("41004", "user or user_id. Doesn't exist");
													$apiresults = array("code" => "41004", "result" => $err_msg);
												}
											
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>
