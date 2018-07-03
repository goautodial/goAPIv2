<?php
/**
 * @file        goEditCampaign.php
 * @brief       API to edit specific Phone
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Milo  <james@goautodial.com>
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
    
    @include_once("goAPI.php");
 
    // POST or GET Variables
    $extension = $astDB->escape($_REQUEST['extension']);
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
	$gmt = $astDB->escape($_REQUEST['gmt']);
   
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
         
    // Default values 
    $defActive 		= array("Y","N");
	$defProtocol	= array('SIP','Zap','IAX2','EXTERNAL');
    $defStatus 		= array('ACTIVE','SUSPENDED','CLOSED','PENDING','ADMIN');

	if ($protocol == "EXTERNAL") { $phone_pass = ""; }
		else { $phone_pass = $pass; }
		
    //Error Checking Next
    if(!isset($session_user) || is_null($session_user)){
        $apiresults = array("result" => "Error: Session User Not Defined.");
    }elseif($extension == null) {
        $apiresults = array("result" => "Error: Set a value for Extension.");
    } elseif(!in_array($status,$defStatus) && $status != null) {
        $apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
    } elseif(!in_array($active,$defActive) && $active != null) {
        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
    } elseif(!in_array($protocol,$defProtocol) && $protocol != null) {
        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $server_ip)){
        $apiresults = array("result" => "Error: Special characters found in server_ip");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $pass)){
        $apiresults = array("result" => "Error: Special characters found in password");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $dialplan_number)){
        $apiresults = array("result" => "Error: Special characters found in dialplan_number");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $voicemail_id)){
        $apiresults = array("result" => "Error: Special characters found in voicemail_id");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $status)){
        $apiresults = array("result" => "Error: Special characters found in status");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $fullname)){
        $apiresults = array("result" => "Error: Special characters found in fullname");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $messages)){
        $apiresults = array("result" => "Error: Special characters found in messages");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $old_messages)){
        $apiresults = array("result" => "Error: Special characters found in old_messages");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $user_group)){
        $apiresults = array("result" => "Error: Special characters found in user_group");
    } else {       
        if (checkIfTenant($log_group, $goDB)) { $astDB->where("user_group", $log_group); }

        $astDB->where("extension", $extension);
        $fresults = $astDB->getOne("phones");

        if($astDB->count > 0) {
			$rpasshash = $astDB->getOne("system_settings");
			$pass_hash_enabled = $rpasshash["pass_hash_enabled"];
			
			if  ($pass != NULL) {
				$data = Array(
					"server_ip" => $server_ip,
					"pass" => $phone_pass,
					"protocol" => $protocol,
					"dialplan_number" => $dialplan_number,
					"voicemail_id" => $voicemail_id,
					"status" => $status,
					"active" => $active,
					"fullname" => $fullname,
					"messages" => $messages,
					"old_messages" => $old_messages,
					"user_group" => $user_group,
					"conf_secret" => $phone_pass
				);
				
				$goDB->where("setting", "GO_agent_wss_sip");
				$querygo = $goDB->getOne("settings");
				$realm = $querygo["value"];
					
				if ($pass_hash_enabled > 0) {
					$ha1 = md5("{$extension}:{$realm}:{$pass}");
					$ha1b = md5("{$extension}@{$realm}:{$realm}:{$pass}");
					$pass = '';
				}	
				
				$datakam = array(
					//"username" => $extension,
					//"domain" => $domain,
					"password" => $pass,
					"ha1" => $ha1,
					"ha1b" => $ha1b
				);
				
				$kamDB->where("username", $extension);
				$kam_update = $kamDB->update("subscriber", $datakam);
				
			} else {
				$data = Array(
					"server_ip" => $server_ip,
					"protocol" => $protocol,
					"dialplan_number" => $dialplan_number,
					"voicemail_id" => $voicemail_id,
					"status" => $status,
					"active" => $active,
					"fullname" => $fullname,
					"messages" => $messages,
					"old_messages" => $old_messages,
					"user_group" => $user_group
				);			
			}
			
			$astDB->where("extension", $extension);
			$main_update = $astDB->update("phones", $data);

			if ($protocol != "EXTERNAL") { $rebuild = rebuildconfQuery($astDB, $server_ip); }

			// Admin logs
			$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Phone: $extension", $log_group, $main_update . $kam_update);

			if($main_update){
				$apiresults = array("result" => "success");
			}else{
				$apiresults = array("result" => "Error: Failed to Update");
			}
    	} else {
            $apiresults = array("result" => "Error: Phone doesn't  exist.");
    	}
    }
?>
