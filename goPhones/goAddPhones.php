<?php
/**
 * @file 		goAddPhones.php
 * @brief 		API to add phone
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

	@include_once ("goAPI.php");
	include_once ("../licensed-conf.php");
	
	// POST or GET Variables
    $orig_extension = $astDB->escape($_REQUEST['extension']);
    $server_ip = $astDB->escape($_REQUEST['server_ip']);
    $pass =    $astDB->escape($_REQUEST['pass']);
    $protocol = $astDB->escape($_REQUEST['protocol']);
    $dialplan_number = $astDB->escape($_REQUEST['dialplan_number']);
    $voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);
    $status = $astDB->escape($_REQUEST['status']);
    $active = $astDB->escape($_REQUEST['active']);
    $fullname = $astDB->escape($_REQUEST['fullname']);
    $messages = !isset($_REQUEST['messages']) ? 0 : $astDB->escape($_REQUEST['messages']);
    $old_messages = !isset($_REQUEST['old_messages']) ? 0 : $astDB->escape($_REQUEST['old_messages']);
    $user_group = $astDB->escape($_REQUEST['user_group']);
    $log_ip = $astDB->escape($_REQUEST['log_ip']);
	$gmt = $astDB->escape($_REQUEST['gmt']);
	
	if(isset($_REQUEST['seats'])) { $seats = $astDB->escape($_REQUEST['seats']); }
		else { $seats = 1; }
	if ($protocol == "EXTERNAL") { $phone_pass = ""; }
		else { $phone_pass = $pass; }
		
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB); 
	$log_ip = $astDB->escape($_REQUEST['log_ip']);
	
	$defStatus = array('ACTIVE','SUSPENDED','CLOSED','PENDING,ADMIN');
	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
	$defActive = array("Y","N");

  	if(empty($session_user)){
  		$apiresults = array("result" => "Error: Session User Not Defined.");
  	}elseif(empty($orig_extension)) {
		$apiresults = array("result" => "Error: Set a value for Extension.");
	} elseif(!in_array($status,$defStatus)) {
		$apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
	} elseif(!in_array($active,$defActive)) {
		$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif(!in_array($protocol,$defProtocol)) {
		$apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $orig_extension)){
		$apiresults = array("result" => "Error: Special characters found in extension");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip) || $server_ip == null){
		$apiresults = array("result" => "Error: Special characters found in server_ip or must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass) || $pass == null){
		$apiresults = array("result" => "Error: Special characters found in password and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $dialplan_number) || $dialplan_number == null){
		$apiresults = array("result" => "Error: Special characters found in dialplan_number and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id) || $voicemail_id == null){
		$apiresults = array("result" => "Error: Special characters found in voicemail_id and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status) || $status == null){
		$apiresults = array("result" => "Error: Special characters found in status and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname) || $fullname == null){
		$apiresults = array("result" => "Error: Special characters found in fullname and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $messages) || $messages == null){
		$apiresults = array("result" => "Error: Special characters found in messages and must not be null");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $old_messages) || $old_messages == null){
		$apiresults = array("result" => "Error: Special characters found in old_messages and must not be null");
	} else {
		// Check License Seats //		
		$astDB->where("user", DEFAULT_USERS, "NOT IN");
		$astDB->where("user_level", 4, "!=");
		$num_users = $astDB->getValue("vicidial_users", "count(*)", null);
			
		// Check if DB Licensed Seats Exists //
		$goDB->where("setting", "GO_licensed_seats");
		$fetch_license = $goDB->getOne("settings");
		//$check_db_license = "SELECT * FROM settings WHERE setting = 'GO_licensed_seats' LIMIT 1;";	
				
		if ($goDB->count > 0) { $licensedSeats = $fetch_license['value']; }	
		else { $licensedSeats = $config["licensedSeats"]; }			
		
		$error_count = 0;
		if ($num_users <= $licensedSeats || $licensedSeats == "0") {		
			$rpasshash = $astDB->getOne("system_settings");
			$pass_hash_enabled = $rpasshash["pass_hash_enabled"];
			$arr_phone = array();
			$add_num = 0;
			
			for ($i=0;$i < $seats;$i++){
				//$a = 1;
				$iterate_number1 = $orig_extension + $add_num;
				
				if ($iterate_number1 > $orig_extension) {
					$extension = $iterate_number1;
				} else {
					$extension = $orig_extension;
					if($last_num_phone === 0 && $seats > 0){
						$orig_extension = $orig_extension."1";
						$last_num_phone = 1;
					}
				}
				
				$add_num = $add_num + 1;
				
				//while ($a >= 1) {
					if (checkIfTenant($log_group, $goDB)) {
						$astDB->where("user_group", $log_group);
					}
					
					$astDB->where("extension", $extension);
					$astDB->getOne("phones", "extension");
					
					if ($astDB->count < 1) {
						$a = 0;	
						$dataPhones = array(
							"extension" => $extension,
							"dialplan_number" => "9999" . $extension,
							"voicemail_id" => $extension,
							"phone_ip" => "",
							"computer_ip" => "",
							"server_ip" => $server_ip,
							"login" => $extension,
							"pass" => $phone_pass,
							"status" => $status,
							"active" => $active,
							"phone_type" => "",
							"fullname" => $fullname,
							"company" => $user_group,
							"picture" => "",
							"protocol" => "EXTERNAL",
							"local_gmt" => -5,
							"outbound_cid" => "0000000000",
							"template_id" => "--NONE--",
							//"conf_override" => $conf_override,
							//"user_group" => $user_group,
							"conf_secret" => $phone_pass,
							"messages" => $messages,
							"old_messages" => $old_messages
						);
							
						$q_insertPhone = $astDB->insert('phones', $dataPhones); // insert record in asterisk.phones
						
						### ADDING IN KAMAILIO DB			
						$goDB->where("setting", "GO_agent_wss_sip");
						$querygo = $goDB->getOne("settings");
						$realm = $querygo["value"];
						/*
						$queryg = "SELECT value FROM settings WHERE setting='GO_agent_wss_sip';";
						*/

						if ($pass_hash_enabled > 0) {
							$ha1 = md5("{$extension}:{$realm}:{$pass}");
							$ha1b = md5("{$extension}@{$realm}:{$realm}:{$pass}");
							$password = '';
						} else {
							$password = $pass;
						}

						$goDB->where("setting", "GO_agent_domain");
						$rowd = $goDB->getOne("settings");
						//$queryd = "SELECT value FROM settings WHERE setting='GO_agent_domain';";							
						$domain = (!is_null($rowd["value"]) || $rowd["value"] !== '') ? $rowd["value"] : 'goautodial.com';	
						
						//$goDB->where("settings", "GO_agent_sip_server");
						//$rowsip = $goDB->getOne("settings");
						//$sip_server = $rowsip["value"];

						//if ($sip_server == "kamailio") {
						$datakam = array(
							"username" => $extension,
							"domain" => $domain,
							"password" => $password,
							"ha1" => $ha1,
							"ha1b" => $ha1b
						);							
						$qkam_insertSubscriber = $kamDB->insert("subscriber", $datakam);
						//}
						
						if ($protocol != "EXTERNAL") { $rebuild = rebuildconfQuery($astDB, $server_ip); }
						$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New Phone: $extension", $log_group, $q_insertPhone . $qkam_insertSubscriber);
						
						$return_extension = $extension;
						array_push($arr_phone, $return_extension);						
					} else {
						$error_count = 1;
						$i = $i - 1;
					}
					//$extension = $extension + $a;
				//}
			}
			
			if ($error_count == 0) {
				$apiresults = array("result" => "success", "rebuild_conf_status" => $rebuild);
			} elseif ($error_count == 1) {
				$err_msg = error_handle("10116");
				$apiresults = array("code" => "10116", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Phone already exist.");
			}
		} else {
			$err_msg = error_handle("10004", "seats. Reached Maximum Licensed Seats!");
			$apiresults = array("code" => "10004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Reached Maximum Licensed Seats!");
		}			
	}
?>
