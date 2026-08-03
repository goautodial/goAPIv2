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

	include_once (__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */


	### POST or GET Variables
	$allow_voicemail_greeting					= $astDB->escape(($_REQUEST["allow_voicemail_greeting"] ?? ''));

	### ERROR CHECKING
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= [
			"result" 						=> "Error: Session User Not Defined."
		];
	} else {

		$resultGet = $astDB->getOne("system_settings", "allow_voicemail_greeting");

		$current_allow_voicemail_greeting = $resultGet["allow_voicemail_greeting"] ?? '';

		if ( $current_allow_voicemail_greeting !== $allow_voicemail_greeting ){
			$result = $current_allow_voicemail_greeting;
			$data 						= [
				"allow_voicemail_greeting"				=> $allow_voicemail_greeting
			];

			$update					= $astDB->update("system_settings", $data);

			if ($update) {
				$apiresults 			= [
					"result" 				=> "success",
					"data" 					=> $data
				];

				if ( $allow_voicemail_greeting ) {
					$act = "Enabled";
				} else {
					$act = "Disabled";
				}

				$log_message = "$act Voicemail Greeting";

			} else {
				$apiresults				= [
					"result" 				=> "Error: Allow voicemail greeting update failed, check your details"
				];

				$log_message = "Failed to Update System Settings: Voicemail Setting";

			}

			$log_id = log_action( $goDB, 'MODIFY', $log_user, $log_ip, $log_message, $log_group, $astDB->getLastQuery() );
		}

		$apiresults = [
			"result"                                => "success",
			"message"				=> "Allow Voicemail Geeting Unchanged"
                ];
	}
?>
