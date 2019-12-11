<?php
/**
 * @file 		goEditSystemSettings.php
 * @brief 		API for editing System Settings
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
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

	include_once ("goAPI.php");
 
	### POST or GET Variables
	$allow_voicemail_greeting					= $astDB->escape($_REQUEST["allow_voicemail_greeting"]); 

	### ERROR CHECKING 					
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} else {
		
		$resultGet = $astDB->getOne("system_settings", "allow_voicemail_greeting");

		if ( $resultGet["allow_voicemail_greeting"] !== $allow_voicemail_greeting ){
			$result = $resultGet["allow_voicemail_greeting"];
			$data 						= array(
				"allow_voicemail_greeting"				=> $allow_voicemail_greeting
			);
			
			$update					= $astDB->update("system_settings", $data);
		
			if ($update) {
				$apiresults 			= array(
					"result" 				=> "success",
					"data" 					=> $data
				);

				if ( $allow_voicemail_greeting ) { 
					$act = "Enabled"; 
				} else { 
					$act = "Disabled";
				}

				$log_message = "$act Voicemail Greeting";

			} else {
				$apiresults				= array(
					"result" 				=> "Error: Allow voicemail greeting update failed, check your details"
				);

				$log_message = "Failed to Update System Settings: Voicemail Setting";

			}

			$log_id = log_action( $goDB, 'MODIFY', $log_user, $log_ip, $log_message, $log_group, $astDB->getLastQuery() );
		}

		$apiresults = array(
			"result"                                => "success",
			"message"				=> "Allow Voicemail Geeting Unchanged"
                ); 
	}
?>
