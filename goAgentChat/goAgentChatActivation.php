<?php
 /**
 * @file 		goAgentChatActivation.php
 * @brief 		API for Agent Chat Activation
 * @copyright 	Copyright (C) 2020 GOautodial Inc.
 * @author		Thom Bernarth Patacsil
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

	$action = $goDB->escape($_REQUEST["action_agent_chat"]);
	
	//$query = "SELECT * FROM smtp_settings LIMIT 1;";
	$rsltv = $goDB->getOne('go_agent_chat');
	$exist = $goDB->getRowCount();
	$err_msg = $goDB->getLastError();

	//$exist = 1;
	if($exist <= 0){
		$query1 = "CREATE TABLE `go_agent_chat_login_details` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `userid` int(11) NOT NULL,
                `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `is_typing` enum('no','yes') NOT NULL,
                PRIMARY KEY (id)
                );";
		
		$query2 = "CREATE TABLE `go_agent_chat` (
                `chatid` int(11) NOT NULL AUTO_INCREMENT,
                `sender_userid` int(11) NOT NULL,
                `reciever_userid` int(11) NOT NULL,
                `message` text NOT NULL,
                `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`loaded` int(1) NOT NULL,
                `status` int(1) NOT NULL,
                PRIMARY KEY (chatid)
                );";

		$query3 = "ALTER TABLE users ADD enable_chat tinyint(1) NULL DEFAULT 1;";

		$query4 = "ALTER TABLE users ADD chat_current_session int(11) NOT NULL;";

		$query5 = "ALTER TABLE users ADD chat_online int(11) NOT NULL;";

		$exec_query1 = $goDB->rawQuery($query1);
		$exec_query2 = $goDB->rawQuery($query2);
		$exec_query3 = $goDB->rawQuery($query3);
		$exec_query4 = $goDB->rawQuery($query4);
		$exec_query5 = $goDB->rawQuery($query5);
		
		if($exec_query1 && $exec_query2 && $exec_query3 && $exec_query4 && $exec_query5){
			// create enable_agent_chat in settings
			//$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_agent_chat';");
			$goDB->where('setting', 'enable_agent_chat');
			$check_settings = $goDB->get('settings');
			$setting_exist = $goDB->getRowCount();
			
			if($setting_exist <= 0){
				//$insert_enable_agent_chat = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_agent_chat', 'agent_chat_settings', '0');");
				$insertData = array(
					'setting' => 'enable_agent_chat',
					'context' => 'agent_chat_settings',
					'value' => '0'
				);
				$insert_enable_smtp = $goDB->insert('settings', $insertData);
			}
			
			$apiresults = array("result" => "success");
			$log_id = log_action($goDB, 'CREATE', $log_user, $ip_address, "Created Agent Chat Settings!", $log_group, $new_smtp_default_query);
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
		}
	}
	
	// second check if enable agent chat exists
	//$check_settings = mysqli_query($linkgo, "SELECT * FROM settings WHERE setting = 'enable_agent_chat';");
	$goDB->where('setting', 'enable_agent_chat');
	$check_settings = $goDB->get('settings');
	$setting_exist = $goDB->getRowCount();
	
	if($setting_exist <= 0){
		//$insert_enable_agent_chat = mysqli_query($linkgo, "INSERT INTO settings (setting, context, value) VALUES('enable_agent_chat', 'agent_chat_settings', '0');");
		$insertData = array(
			'setting' => 'enable_agent_chat',
			'context' => 'agent_chat_settings',
			'value' => '0'
		);
		$insert_enable_smtp = $goDB->insert('settings', $insertData);
	}
	
	$default_action = array(0, 1); // 0 = deactivate, 1 = activate
	if(in_array($action, $default_action)){
		//$action_agent_chat_query = "UPDATE settings SET value = '$action' WHERE setting = 'enable_agent_chat';";
		$updateData = array(
			'value' => $action
		);
		$goDB->where('setting', 'enable_agent_chat');
		$exec_action_agent_chat = $goDB->update('settings', $updateData);
		
		if($goDB->getRowCount() > 0){
			$apiresults = array("result" => "success", "query" => $querythom);
			if($action == 1)
				$act = "Enabled";
			else
				$act = "Disabled";
			$log_id = log_action($goDB, 'UPDATE', $log_user, $ip_address, "$log_user $act Agent Chat Settings!", $log_group, $goDB->getLastQuery());
		}else{
			$apiresults = array("result" => "error", "msq" => $new_agent_chat_default_query);
		}
	}else{
		$apiresults = array("result" => "error", "msg" => $goDB->getLastError());
	}
?>
