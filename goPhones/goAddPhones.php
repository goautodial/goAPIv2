<?php
/**
 * @file 		goAddPhones.php
 * @brief 		API to add phone
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com>
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
    include_once ("../goFunctions.php");
	
	// POST or GET Variables
    $extension = $_REQUEST['extension'];
    $server_ip = $_REQUEST['server_ip'];
    $pass =    $_REQUEST['pass'];
    $protocol = $_REQUEST['protocol'];
    $dialplan_number = $_REQUEST['dialplan_number'];
    $voicemail_id = $_REQUEST['voicemail_id'];
    $status = $_REQUEST['status'];
    $active = $_REQUEST['active'];
    $fullname = $_REQUEST['fullname'];
    $messages = !isset($_REQUEST['messages']) ? 0 : $_REQUEST['messages'];
    $old_messages = !isset($_REQUEST['old_messages']) ? 0 : $_REQUEST['old_messages'];
    $user_group = $_REQUEST['user_group'];
    $ip_address = $_REQUEST['hostname'];
	$gmt = $_REQUEST['gmt'];
	if(isset($_REQUEST['seats']))
        $seats = $_REQUEST['seats'];
	else
		$seats = 1;
	
	$log_user = $session_user;
	
	$defStatus = array('ACTIVE','SUSPENDED','CLOSED','PENDING,ADMIN');
	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
	$defActive = array("Y","N");

  	if(empty($session_user)){
  		$apiresults = array("result" => "Error: Session User Not Defined.");
  	}elseif(empty($extension)) {
		$apiresults = array("result" => "Error: Set a value for Extension.");
	} elseif(!in_array($status,$defStatus)) {
		$apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
	} elseif(!in_array($active,$defActive)) {
		$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif(!in_array($protocol,$defProtocol)) {
		$apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
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
		$groupId = go_get_groupid($session_user, $astDB);
		$error_count = 0;
		
		for($i=0;$i < $seats;$i++){
			$a = 1;
			while($a >= 1){
				if (!checkIfTenant($groupId, $astDB)) {
					$astDB->where("extension", $extension);
					//$ul = "WHERE extension='$extension'";
				} else {
					$astDB->where("extension", $extension);
					$astDB->where("user_group", $groupId);
					//$ul = "WHERE extension='$extension' AND user_group='$groupId'";
				}
				$astDB->getOne("phones", "extension");
				$countResult = $astDB->count;
				//$query_check = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
				
				if($countResult < 1) {
					$a = 0;
					$data = array(
							"extension" => $extension,
							"dialplan_number" => "9999".$extension,
							"voicemail_id" => $extension,
							"phone_ip" => "",
							"computer_ip" => "",
							"server_ip" => $server_ip,
							"login" => $extension,
							"pass" => $pass,
							"status" => $status,
							"active" => $active,
							"phone_type" => "",
							"fullname" => $fullname,
							"company" => $user_group,
							"picture" => "",
							"protocol" => $protocol,
							"local_gmt" => $gmt,
							"outbound_cid" => "0000000000",
							"template_id" => "--NONE--",
							"user_group" => $user_group,
							"messages" => $messages,
							"old_messages" => $old_messages
						);
					$queryAdd = $astDB->insert('phones', $data); // insert record in asterisk.phones

					//$query = "INSERT INTO `phones` (`extension`,  `dialplan_number`,  `voicemail_id`,  `phone_ip`,  `computer_ip`,  `server_ip`,  `login`,  `pass`,  `status`,  `active`,  `phone_type`,  `fullname`,  `company`,  `picture`,  `protocol`,  `local_gmt`,  `outbound_cid`,  `template_id`,    `user_group`,   `messages`,  `old_messages`) VALUES ('$extension',  '9999$extension',  '$extension',  '',  '', '$server_ip',  '$extension',  '$pass',  '$status',  '$active',  '',  '$fullname',  '$user_group',  '',  '$protocol',  '$gmt',  '0000000000',  '--NONE--', '$user_group', '$messages',  '$old_messages');";
					
					### ADDING IN KAMAILIO DB
					
					$fetch_passhash = $astDB->getOne("system_settings", "pass_hash_enabled");
					$pass_hash_enabled = $fetch_passhash["pass_hash_enabled"];
					/*
					$query_passhash = "select pass_hash_enabled from system_settings";
					*/
					$pass_hash = '';
					if($pass_hash_enabled > 0){
						$cwd = $_SERVER['DOCUMENT_ROOT'];
						$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
						$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
					}
					$goDB->where("settings", "GO_agent_wss_sip");
					$fetch_GO_agent_wss_sip = $goDB->getOne("settings", "value");
					$realm = $fetch_GO_agent_wss_sip["value"];
					/*
					$queryg = "SELECT value FROM settings WHERE setting='GO_agent_wss_sip';";
					*/

					$kamha1fields = '';
					$kamha1values = '';
					if ($pass_hash_enabled > 0) {
						$ha1 = md5("{$extension}:{$realm}:{$pass}");
						$ha1b = md5("{$extension}@{$realm}:{$realm}:{$pass}");
						$kamha1fields = ", ha1, ha1b";
						$kamha1values = ", '{$ha1}', '{$ha1b}'";
						$pass = '';
					}
					
					$goDB->where("settings", "GO_agent_domain");
					$fetch_GO_agent_domain = $goDB->getOne("settings", "value");
					$rowd = $fetch_GO_agent_domain["value"];
					/*
					$queryd = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
					*/
					$domain = (!is_null($rowd['value']) || $rowd['value'] !== '') ? $rowd['value'] : 'goautodial.com';
					
					$goDB->where("settings", "GO_agent_sip_server");
					$fetch_GO_agent_sip_server = $goDB->getOne("settings", "value");
					$rowk = $fetch_GO_agent_sip_server["value"];
					/*
					$queryk = "SELECT value FROM settings WHERE setting='GO_agent_sip_server';";
					*/
					if($rowk === "kamailio"){
						$data = array(
								"username" => $extension,
								"domain" => $domain,
								"password{$kamha1fields}" => $pass{$kamha1values}
							);
						$queryInsertSubscriber = $kamDB->insert('subscriber', $data);
						/*
						$kamailioq = "INSERT INTO subscriber (username, domain, password{$kamha1fields}) VALUES ('$extension','$domain','$pass'{$kamha1values});";
						*/
					}
					
					$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added New Phone: $extension", $groupId, $query);
					//$return_query[] = $query_check;
				} else {
					$error_count = $error_count + 1;
					//$apiresults = array("result" => "Error: Phone already exist.");
				}
				$extension = $extension + $a;
			}
		}

		$rebuild = rebuildconfQuery($astDB, $server_ip);

		if($resultQuery){
			$apiresults = array("result" => "success", "errors" => $error_count, "rebuild_conf_status" => $rebuild);
		}
	}