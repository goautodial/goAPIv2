<?php
/**
 * @file        goEditCampaign.php
 * @brief       API to edit specific Phone
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Jerico James Milo
 * @author      Alexander Jim H. Abenoja
 * @author		Demian Lizandro A. Biscocho
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

    include_once(__DIR__ . "/goAPI.php");

    // POST or GET Variables
    $extension 											= $astDB->escape(($_REQUEST['extension'] ?? ''));
    $server_ip 											= $astDB->escape(($_REQUEST['server_ip'] ?? ''));
    $pass 												= $astDB->escape(($_REQUEST['pass'] ?? ''));
    $protocol 											= $astDB->escape(($_REQUEST['protocol'] ?? ''));
    $dialplan_number 									= $astDB->escape(($_REQUEST['dialplan_number'] ?? ''));
    $voicemail_id 										= $astDB->escape(($_REQUEST['voicemail_id'] ?? ''));
    $status 											= $astDB->escape(($_REQUEST['status'] ?? ''));
    $active 											= $astDB->escape(($_REQUEST['active'] ?? ''));
    $fullname 											= $astDB->escape(($_REQUEST['fullname'] ?? ''));
    $messages 											= isset($_REQUEST['messages']) ? $astDB->escape($_REQUEST['messages']) : 0;
    $old_messages 										= isset($_REQUEST['old_messages']) ? $astDB->escape($_REQUEST['old_messages']) : 0;
    $user_group 										= isset($_REQUEST['user_group']) ? $astDB->escape($_REQUEST['user_group']) : null;
	$gmt 												= isset($_REQUEST['gmt']) ? $astDB->escape($_REQUEST['gmt']) : null;
	$phone_pass											= ($protocol == "EXTERNAL") ? "" : $pass;

    // Default values
    $defActive 											= ["Y","N"];
	$defProtocol										= ['SIP','Zap','IAX2','EXTERNAL'];
    $defStatus 											= ['ACTIVE','SUSPENDED','CLOSED','PENDING','ADMIN'];

    // Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI User Not Defined."
		];
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI Password Not Defined."
		];
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= [
			"result" 										=> "Error: Session User Not Defined."
		];
	} elseif (empty($extension) || is_null($extension)) {
		$apiresults										= [
			"result" 										=> "Error: Phone Extension Not Defined."
		];
	} elseif (!in_array($status, (is_array($defStatus) ? $defStatus : []))) {
		$apiresults 									= [
			"result" 										=> "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only."
		];
	} elseif (!in_array($active, (is_array($defActive) ? $defActive : []))) {
		$apiresults 									= [
			"result" 										=> "Error: Default value for active is Y or N only."
		];
	} elseif (!in_array($protocol, (is_array($defProtocol) ? $defProtocol : []))) {
		$apiresults 									= [
			"result" 										=> "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $server_ip)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in server_ip."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $pass)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in password."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $dialplan_number)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in dialplan_number."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $voicemail_id)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in voicemail_id."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in status."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $fullname)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in fullname."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $messages)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in messages."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $old_messages)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in old_messages."
		];
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");

		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];

		if ($goapiaccess > 0 && $userlevel > 7) {
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant($log_group, $goDB)) ? 1 : 0;

			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper((string) $log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}
			}

			$astDB->where("extension", $extension);
			$astDB->getOne("phones");

			if ($astDB->count > 0) {
				$rpasshash 								= $astDB->getOne("system_settings");
				$pass_hash_enabled 						= $rpasshash["pass_hash_enabled"];

				if ($pass != NULL) {
					$data 								= [
						"server_ip" 						=> $server_ip,
						"pass" 								=> $phone_pass,
						"protocol" 							=> $protocol,
						"dialplan_number" 					=> $dialplan_number,
						"voicemail_id" 						=> $voicemail_id,
						"status" 							=> $status,
						"active" 							=> $active,
						"fullname" 							=> $fullname,
						"messages" 							=> $messages,
						"old_messages" 						=> $old_messages,
						"user_group" 						=> $user_group,
						"conf_secret" 						=> $phone_pass
					];

					$goDB->where("setting", "GO_agent_wss_sip");
					$querygo 							= $goDB->getOne("settings");
					$realm 								= $querygo["value"];

					$ha1 								= '';
				$ha1b 								= '';

				if ($pass_hash_enabled > 0) {
						$ha1 							= md5("{$extension}:{$realm}:{$pass}");
						$ha1b 							= md5("{$extension}@{$realm}:{$realm}:{$pass}");
						$pass 							= '';
					}

					$datakam 							= [
						"password" 							=> $pass,
						"ha1" 								=> $ha1,
						"ha1b"	 							=> $ha1b
					];

					$kamDB->where("username", $extension);
					$kamDB->update("subscriber", $datakam);

					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $extension", $log_group, $kamDB->getLastQuery());

				} else {
					$data 								= [
						"server_ip" 						=> $server_ip,
						"protocol" 							=> $protocol,
						"dialplan_number" 					=> $dialplan_number,
						"voicemail_id" 						=> $voicemail_id,
						"status" 							=> $status,
						"active" 							=> $active,
						"fullname" 							=> $fullname,
						"messages" 							=> $messages,
						"old_messages" 						=> $old_messages,
						"user_group" 						=> $user_group
					];
				}

				$astDB->where("extension", $extension);
				$astDB->update("phones", $data);
				$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Phone: $extension", $log_group, $astDB->getLastQuery());

				if ($protocol != "EXTERNAL") {
					$rebuild 							= rebuildconfQuery($astDB, $server_ip);
				}

				$apiresults 							= [
					"result" 								=> "success"
				];
			} else {
				$apiresults 							= [
					"result" 								=> "Error: Phone doesn't  exist."
				];
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 										=> "10001",
				"result" 									=> $err_msg
			];
		}
	}

?>
