<?php
    ############################################################
    #### Name: goAddSMTPSettings.php 			####
    #### Description: API to Activate SMTP Settings 			####
    #### Version: 4.0 			####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016 			####
    #### Written by: Alexander Jim H. Abenoja 			####
    #### License: AGPLv2 			####
    ############################################################
    
    include_once ("../goFunctions.php");
		
		$action = mysqli_real_escape_string($linkgo, $_REQUEST["action_smtp"]);
		
		$ip_address = mysqli_real_escape_string($linkgo, $_REQUEST['hostname']);
		$log_user = mysqli_real_escape_string($linkgo, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($linkgo, $_REQUEST['log_group']);
		
		$query = "SELECT * FROM smtp_settings LIMIT 1;";
		$rsltv = mysqli_query($linkgo, $query);
		$exist = mysqli_num_rows($rsltv);
		
		if($exist <= 0){
			$new_smtp_default_query = "CREATE TABLE IF NOT EXISTS smtp_settings ( debug INT(1), timezone VARCHAR(250), ipv6_support INT(1), host VARCHAR(120), port INT(4), smtp_security VARCHAR(3), smtp_auth INT(1), username varchar(120), password varchar(120));
			INSERT INTO smtp_settings(debug, timezone, ipv6_support, host, port, smtp_security, smtp_auth, username, password) VALUES('0', 'Etc/UTC', '0', 'smtp.gmail.com', '587', 'tls', '1', '', '');
			ALTER TABLE `messages_outbox` ADD `external_recepient` VARCHAR(124) AFTER `user_to`;";
			$exec_new_smtp = mysqli_query($linkgo, $new_smtp_default_query);
						
			if($exec_new_smtp){
				// create enable_smtp in settings
				$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_smtp';");
				$setting_exist = mysqli_num_rows($check_settings);
				
				if($setting_exist <= 0){
					$insert_enable_smtp = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_smtp', 'smtp_settings', '0');");
				}
				
				$apiresults = array("result" => "success");
				$log_id = log_action($linkgo, 'CREATE', $log_user, $ip_address, "Created SMTP Settings!", $log_group, $new_smtp_default_query);
			}else{
				$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
			}
		}
		
		// second check if enable smtp exists
		$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_smtp';");
		$setting_exist = mysqli_num_rows($check_settings);
		
		if($setting_exist <= 0){
			$insert_enable_smtp = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_smtp', 'smtp_settings', '0');");
		}
		
		$default_action = array(0, 1); // 0 = deactivate, 1 = activate
		if(in_array($action, $default_action)){
			$action_smtp_query = "UPDATE settings SET value = '$action' WHERE setting = 'enable_smtp';";
			$exec_action_smtp = mysqli_query($linkgo, $action_smtp_query);
			
			if($exec_action_smtp){
				$apiresults = array("result" => "success");
				if($action == 1)
					$act = "Enabled";
				else
					$act = "Disabled";
				$log_id = log_action($linkgo, 'UPDATE', $log_user, $ip_address, "$log_user $act SMTP Settings!", $log_group, $action_smtp_query);
			}else{
				$apiresults = array("result" => "error");
			}
		}else{
			$apiresults = array("result" => "error");
		}
?>