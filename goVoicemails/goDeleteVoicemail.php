<?php
/**
 * @file 		goDeleteVoicemail.php
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
	$voicemail_id 						= $astDB->escape($_REQUEST['voicemail_id']);

	### ERROR CHECKING 					
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif ($voicemail_id == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Voicemail ID."
		);
	} else {		
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}
		
		// check voicemail ID if valid
		$astDB->where('voicemail_id', $voicemail_id);
		$astDB->getOne('vicidial_voicemail');

		if ($astDB->count > 0) {
			$astDB->where('voicemail_id', $voicemail_id);
			$astDB->delete('vicidial_voicemail');			

			$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Voicemail ID: $voicemail_id", $log_group, $astDB->getLastQuery());			
			$apiresults 				= array(
				"result" 					=> "success"
			);
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: Voicemail doesn't exist."
			);
		}
	}
?>
