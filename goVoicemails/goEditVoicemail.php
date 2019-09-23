<?php
/**
 * @file 		goEditVoicemail.php
 * @brief 		API for editing Voicemails
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Chris Lomuntad
 * @author		Jeremiah Sebastian Samatra
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

	include_once ("goAPI.php");
 
	### POST or GET Variables
	$voicemail_id 						= $astDB->escape($_REQUEST["voicemail_id"]);
	$pass 							= $astDB->escape($_REQUEST["pass"]);
	$fullname 						= $astDB->escape($_REQUEST["fullname"]);
	$email 							= $astDB->escape($_REQUEST["email"]);
	$active 						= $astDB->escape(strtoupper($_REQUEST["active"]));
	$delete_vm_after_email 					= $astDB->escape($_REQUEST["delete_vm_after_email"]);
	$voicemail_greeting					= $astDB->escape($_REQUEST["voicemail_greeting"]); 

	### Default values
    $defActive 							= array(
		"Y",
		"N"
	); 

	$defDelVM 							= array(
		"N",
		"Y"
	);
	
	### ERROR CHECKING 					
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif ($voicemail_id == null || strlen($voicemail_id) < 3) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Voicemail ID."
		);
	} elseif (preg_match("/[\"^£$%&*()}{@#~?><>,|=_+¬-]/", $fullname) || $fullname == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Special characters found in fullname and must not be empty"
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$apiresults 					= array(
			"result" 						=> "Error: Default value for active is Y or N only."
		);
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$apiresults 					= array(
			"result" 						=> "Error: Invalid email format."
		);
	} elseif (!in_array($delete_vm_after_email,$defDelVM) && $delete_vm_after_email != null) {
		$apiresults 					= array(
			"result" 						=> "Error: Default value for delete_vm_after_email is Y or N only."
		);
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}	
		
		$astDB->where("voicemail_id", $voicemail_id);
		$query 							= $astDB->getOne("vicidial_voicemail");
		
		if ($astDB->count > 0) {				
			foreach ($query as $fresults) {
				$dataVM_id			= $fresults["voicemail_id"];
				$dataVM_pass 			= $fresults["pass"];
				$dataactive 			= $fresults["active"];
				$datafullname 			= $fresults["fullname"];
				$dataemail 			= $fresults["email"];
				$datadeleteVMemail 		= $fresults["delete_vm_after_email"];
				$dataVM_greeting		= $fresults["voicemail_greeting"];
			}
			
			if ($pass == null) { 
				$pass 					= $dataVM_pass;
			}
			if ($active == null) {
				$active 				= $dataactive;
			}
			if ($fullname == null) {
				$fullname 				= $datafullname;
			}
			if ($email == null) {
				$email 					= $dataemail;
			}
			if ($delete_vm_after_email == null) {
				$delete_vm_after_email 	= $datadeleteVMemail;
			}
			if ($voicemail_greeting == null) {
                                $voicemail_greeting                     = $dataVM_greeting;
                        }

			$data 						= array(
				"pass" 						=> $pass,
				"fullname" 					=> $fullname,
				"email" 					=> $email,
				"active" 					=> $active,
				"delete_vm_after_email" 			=> $delete_vm_after_email,
				"voicemail_greeting"				=> $voicemail_greeting
			);
			
			$astDB->where("voicemail_id", $voicemail_id);
			$q_update					= $astDB->update("vicidial_voicemail", $data);
			$log_id 					= log_action($goDB, "MODIFY", $log_user, $log_ip, "Modified Voicemail ID: $voicemail_id", $log_group, $astDB->getLastQuery());
			
			if ($query) {
				$apiresults 			= array(
					"result" 				=> "success",
					"data" 					=> $q_update
				);
			} else {
				$apiresults				= array(
					"result" 				=> "Error: Add failed, check your details"
				);
			} 
			
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: Voicemail doesn't exist"
			);
		}
	}
?>
