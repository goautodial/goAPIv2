<?php
   ####################################################
   #### Name: goEditUser.php	                   ####
   #### Description: API to edit specific user     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");

    ### Check file is existed
	if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
        	$conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
	} elseif (file_exists("/etc/goautodial.conf")) {
        	$conf_path = "/etc/goautodial.conf";
	} else {
		$apiresults = array("result" => "Error: File goautodial.conf not found.");
	}
 
    ### POST or GET Variables
   //     $values = $_REQUEST['items'];
		
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

		
    ### Default Values
	$defActive = array("Y","N");
	$defmodify_same_user_level = array("Y","N");	

    ### Error Checking
        if($user == null && $userid == null) {
                $apiresults = array("result" => "Error: Set a value for User ID.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user)){
                $apiresults = array("result" => "Error: Special characters found in user");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
                $apiresults = array("result" => "Error: Special characters found in password");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $full_name)){
                $apiresults = array("result" => "Error: Special characters found in full_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_login)){
                $apiresults = array("result" => "Error: Special characters found in phone_login");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_pass)){
                $apiresults = array("result" => "Error: Special characters found in phone_pass");
        } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                if(!in_array($modify_same_user_level,$defmodify_same_user_level) && $modify_same_user_level != null) {
                        $apiresults = array("result" => "Error: Default value for modify_same_user_level is Y or N only.");
                } else {
		
		if($user_level < 1 && $user_level!=null || $user_level > 9 && $user_level!= null) {
                        $apiresults = array("result" => "Error: User Level Value should be in between 1 and 9");
                } else {

                if($VARSERVTYPE == "gofree" && $hotkeys_active != null) {
                        $apiresults = array("result" => "Error: hotkeys is disabled");
                } else {
				
                $group_ug = go_get_groupid($goUser);
                if($group_ug !== "ADMIN" && $modify_same_user_level != null) {
                        $apiresults = array("result" => "Error: modify_same_user_level is disabled");
                } else {

                $groupId = go_get_groupid($goUser);

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
				
        #### Check User Group if valid
		if($user_group != null){
                $query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);
		}
                $queryUserCheck = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ulUser ORDER BY user ASC LIMIT 1;";
                $rsltvCheck = mysqli_query($link, $queryUserCheck);
                $countCheckResult = mysqli_num_rows($rsltvCheck);
		
                if($countCheckResult > 0) {

                        while($fresults = mysqli_fetch_array($rsltvCheck, MYSQLI_ASSOC)){
                                $dataUserLevel = $fresults['user_level'];
                                $dataUserGroup = $fresults['user_group'];
                                $dataUser = $fresults['user'];
                        }
				if( $modify_same_user_level == "Y") {
						$modify_same_user_level = 0;
				} else {
						$modify_same_user_level = 1;
				}
			

                           /*     $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {

                                                if ($var!="user")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysqli_real_escape_string($val))."', ";

                                                if ($var=="user")
                                                        $user="$val";

                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
			    */
                	if($countResult <= 0 && $user_group!=null) {
                        	$apiresults = array("result" => "Error: User Group doesn't exist");
               	 	} else {
                              //  $query = "UPDATE vicidial_users SET $itemSQL WHERE user='$user';";
                              //  $resultQuery = mysqli_query($link, $query);
		
				## Password Encryption
						$cwd = $_SERVER['DOCUMENT_ROOT'];
						$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
						$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
						
						if($pass != NULL){
								$query_passhash = "select pass_hash_enabled from system_settings";
								$exec_query = mysqli_query($link, $query_passhash);
								$fetch_pass_hash_enabled = mysqli_fetch_array($exec_query);
								
								if($fetch_pass_hash_enabled['pass_hash_enabled'] == "1"){
                                    $pass_query = "`pass_hash` = '$pass_hash', `pass` = '$pass', `phone_pass` = '$phone_pass', ";		
								}else{
									$pass_query = "`pass_hash` = '$pass_hash', `pass` = '$pass', `phone_pass` = '$phone_pass', ";
								}
								
								$queryUpdatePhones = "UPDATE `phones` SET `pass` = '$pass', `conf_secret` = '$pass' WHERE `extension` = '$phone_login';";
								$resultQueryUser = mysqli_query($link, $queryUpdatePhones);
								
								$kamialioq = "UPDATE `subscriber` SET `password` = '$pass' WHERE `username` = '$phone_login';";
								$resultkam = mysqli_query($linkgokam, $kamialioq);
								
						}else{
								$pass_query = "";
						}
						
						if($phone_login != NULL){
								$phonelogin_query = "`phone_login` = '$phone_login', ";
						}else{
								$phonelogin_query = "";
						}

				if($userid != NULL){
						$queryUpdateUser = "UPDATE `vicidial_users` SET $pass_query `full_name` = '$full_name',  $phonelogin_query  `user_group` = '$user_group',  `active` = '$active',
								`hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level', `vdc_agent_api_access` = '$vdc_agent_api_access', `agent_choose_ingroups` = '$agent_choose_ingroups',
								`vicidial_recording_override` = '$vicidial_recording_override', `vicidial_transfers` = '$vicidial_transfers', `closer_default_blended` = '$closer_default_blended', `agentcall_manual` = '$agentcall_manual', `scheduled_callbacks` = '$scheduled_callbacks', `agentonly_callbacks` = '$agentonly_callbacks', 
								`modify_same_user_level` = '$modify_same_user_level', `email` = '$email', `agent_lead_search_override` = '$agent_lead_search_override'  $voicemail_query 
								WHERE `user_id` = '$userid';";
								
                                                $queryUserIDGo = "SELECT userid from users WHERE userid='$userid'";
                                                $resultQueryUserIDGo = mysqli_query($linkgo, $queryUserIDGo);
                                                $rUserIDGo = mysqli_fetch_array($resultQueryUserIDGo, MYSQLI_ASSOC);
                                                $countResultGo = mysqli_num_rows($resultQueryUserIDGo);
                                                //$userIDGo = $rUserIDGo['userid'];
                                                
                                                if ($countResultGo > 0){
                                                    $queryUpdateUserGo = "UPDATE users SET avatar = '$avatar' WHERE userid ='$userid'";                                                    
                                                } else {
                                                    $queryUpdateUserGo = "INSERT INTO users (userid, name, avatar) VALUES ('$userid', '$dataUser', '$avatar')";
                                                }		                                                
				}else{
						$queryUpdateUser = "UPDATE `vicidial_users` SET $pass_query `full_name` = '$full_name',  $phonelogin_query  `user_group` = '$user_group',  `active` = '$active',
								`hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level', `vdc_agent_api_access` = '$vdc_agent_api_access', `agent_choose_ingroups` = '$agent_choose_ingroups',
								`vicidial_recording_override` = '$vicidial_recording_override', `vicidial_transfers` = '$vicidial_transfers', `closer_default_blended` = '$closer_default_blended', `agentcall_manual` = '$agentcall_manual', `scheduled_callbacks` = '$scheduled_callbacks', `agentonly_callbacks` = '$agentonly_callbacks', 
								`modify_same_user_level` = '$modify_same_user_level', `email` = '$email', `agent_lead_search_override` = '$agent_lead_search_override'  $voicemail_query 
								WHERE `user` = '$user';";
								
                                                $queryUserIDGo = "SELECT name from users WHERE name='$user'";
                                                $resultQueryUserIDGo = mysqli_query($linkgo, $queryUserIDGo);
                                                $rUserIDGo = mysqli_fetch_array($resultQueryUserIDGo, MYSQLI_ASSOC);
                                                $countResultGo = mysqli_num_rows($resultQueryUserIDGo);
                                                //$userGo = $rUserIDGo['user'];
                                                
                                                if ($countResultGo > 0){
                                                    $queryUpdateUserGo = "UPDATE users SET avatar ='$avatar' WHERE name ='$user'";                                                    
                                                } else {
                                                    $queryUpdateUserGo = "INSERT INTO users (name, avatar) VALUES ('$user', '$avatar')";
                                                }								                                                
				}
				$resultQueryUser = mysqli_query($link, $queryUpdateUser);
				$resultQueryUserGo = mysqli_query($linkgo, $queryUpdateUserGo);
				
		/*	
        $queryPhoneUpdate = "UPDATE `phones` SET `pass` = '$pass',  `conf_secret` = '$pass' WHERE `login` = '$phone_login'";

				$resultQueryPhoneUpdate = mysqli_query($link, $queryPhoneUpdate);
		*/

        $queryJSIUpdate = "UPDATE justgovoip_sippy_info SET web_password='$phone_pass' where carrier_id='$user_group'";

				$resultQueryJSIUpdate = mysqli_query($link, $queryJSIUpdate);


	### Admin logs
//                                        $SQLdate = date("Y-m-d H:i:s");
//                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY User $user','UPDATE vicidial_users SET user=$user,pass=$pass,full_name=$full_name,phone_login=$phone_login,phone_pass=$phone_pass,user_group=$user_group,active=$active,hotkeys_active=
//										,user_level=$user_level,modify_same_user_level=$modify_same_user_level');";
//                                        $rsltvLog = mysqli_query($link, $queryLog);
				if ($userid != NULL) {
					$result = mysqli_query($link, "SELECT user FROM vicidial_users WHERE user_id='$userid';");
					$userInfo = mysqli_fetch_array($result, MYSQLI_ASSOC);
					$user = $userInfo['user'];
				}
				
				$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified User: $user", $log_group, $queryUpdateUser);


				if($resultQueryUser == false){
				$apiresults = array("result" => $queryUpdateUser);
				} else {	
				$apiresults = array("result" => "success");
				} 
			}
				} else {
					$apiresults = array("result" => "Error: User doesn't exist.", "USER->" => $userid);
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
