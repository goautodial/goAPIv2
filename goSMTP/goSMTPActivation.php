<?php
 /**
 * @file 		goSMTPActivation.php
 * @brief 		API for SMTP
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
*/

	$action = $goDB->escape($_REQUEST["action_smtp"]);
	
	$ip_address = $goDB->escape($_REQUEST['hostname']);
	$log_user = $goDB->escape($_REQUEST['log_user']);
	$log_group = $goDB->escape($_REQUEST['log_group']);
	
	//$query = "SELECT * FROM smtp_settings LIMIT 1;";
	$rsltv = $goDB->getOne('smtp_settings');
	$exist = $goDB->getRowCount();
	
	if($exist <= 0){
		$new_smtp_default_query = "CREATE TABLE IF NOT EXISTS smtp_settings ( debug INT(1), timezone VARCHAR(250), ipv6_support INT(1), host VARCHAR(120), port INT(4), smtp_security VARCHAR(3), smtp_auth INT(1), username varchar(120), password varchar(120));
		INSERT INTO smtp_settings(debug, timezone, ipv6_support, host, port, smtp_security, smtp_auth, username, password) VALUES('0', 'Etc/UTC', '0', 'smtp.gmail.com', '587', 'tls', '1', '', '');
		ALTER TABLE `messages_outbox` ADD `external_recepient` VARCHAR(124) AFTER `user_to`;";
		$exec_new_smtp = $goDB->rawQuery($new_smtp_default_query);
					
		if($exec_new_smtp){
			// create enable_smtp in settings
			//$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_smtp';");
			$goDB->where('setting', 'enable_smtp');
			$check_settings = $goDB->get('settings');
			$setting_exist = $goDB->getRowCount();
			
			if($setting_exist <= 0){
				//$insert_enable_smtp = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_smtp', 'smtp_settings', '0');");
				$insertData = array(
					'setting' => 'enable_smtp',
					'context' => 'smtp_settings',
					'value' => '0'
				);
				$insert_enable_smtp = $goDB->insert('settings', $insertData);
			}
			
			$apiresults = array("result" => "success");
			$log_id = log_action($goDB, 'CREATE', $log_user, $ip_address, "Created SMTP Settings!", $log_group, $new_smtp_default_query);
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
		}
	}
	
	// second check if enable smtp exists
	//$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_smtp';");
	$goDB->where('setting', 'enable_smtp');
	$check_settings = $goDB->get('settings');
	$setting_exist = $goDB->getRowCount();
	
	if($setting_exist <= 0){
		//$insert_enable_smtp = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_smtp', 'smtp_settings', '0');");
		$insertData = array(
			'setting' => 'enable_smtp',
			'context' => 'smtp_settings',
			'value' => '0'
		);
		$insert_enable_smtp = $goDB->insert('settings', $insertData);
	}
	
	$default_action = array(0, 1); // 0 = deactivate, 1 = activate
	if(in_array($action, $default_action)){
		//$action_smtp_query = "UPDATE settings SET value = '$action' WHERE setting = 'enable_smtp';";
		$updateData = array(
			'value' => $action
		);
		$goDB->where('setting', 'enable_smtp');
		$exec_action_smtp = $goDB->update('settings', $updateData);
		
		if($goDB->getRowCount() > 0){
			$apiresults = array("result" => "success");
			if($action == 1)
				$act = "Enabled";
			else
				$act = "Disabled";
			$log_id = log_action($goDB, 'UPDATE', $log_user, $ip_address, "$log_user $act SMTP Settings!", $log_group, $goDB->getLastQuery());
		}else{
			$apiresults = array("result" => "error");
		}
	}else{
		$apiresults = array("result" => "error");
	}
?>