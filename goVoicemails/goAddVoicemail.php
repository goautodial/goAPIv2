<?php
/**
 * @file 		goAddVoicemail.php
 * @brief 		API for adding voicemails
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Chris Lomuntad 
 * @author     	Jeremiah Sebastian Samatra
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
	$voicemail_id 						= $astDB->escape($_REQUEST['voicemail_id']);
	$pass 								= $astDB->escape($_REQUEST['pass']);
	$fullname 							= $astDB->escape($_REQUEST['fullname']);
	$email 								= $astDB->escape($_REQUEST['email']);
	$user_group 						= $astDB->escape($_REQUEST['user_group']);
	$active 							= $astDB->escape(strtoupper($_REQUEST['active']));

	### Default values
    $defActive 							= array(
		"Y",
		"N"
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
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id)) {
		$apiresults 					= array(
			"result" 						=> "Error: Special characters found in voicemail ID"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname) || $fullname == null) {
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
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			//$astDB->where("user_group", $log_group);
		}					
		
		$astDB->where("voicemail_id", $voicemail_id);
		$astDB->getOne("vicidial_voicemail");
		
		if ($astDB->count > 0) {
			$apiresults 				= array(
				"result" 					=> "Error: Add failed, Duplicate voicemail ID found!"
			);
		} else {
			$data						= array(
				'voicemail_id' 				=> $voicemail_id,
				'pass' 						=> $pass,
				'fullname' 					=> $fullname,
				'active' 					=> $active,
				'email' 					=> $email,
				'user_group' 				=> $user_group
			);
			
			$q_insert					= $astDB->insert('vicidial_voicemail', $data);			
			$log_id 					= log_action($goDB, 'ADD', $log_user, $log_ip, "Added new voicemail. ID: $voicemail_id", $log_group, $astDB->getLastQuery());
			
			if($q_insert){
				rebuildconfQuery($astDB);
				
				$apiresults 			= array(
					"result" 				=> "success",
					"data" 					=> $q_insert
				);
			} else {
				$apiresults				= array(
					"result" 				=> "Error: Add failed, check your details"
				);
			}
		}
	}

?>
