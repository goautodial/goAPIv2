<?php
/**
 * @file        goEditUserStatus.php
 * @brief       API to User and Chat Status
 * @copyright   Copyright (C) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
	$userid = $goDB->escape($_REQUEST['userid']);
	$to_user_id = $goDB->escape($_REQUEST['to_user_id']);
	$chat_action = $goDB->escape($_REQUEST['chat_action']);

	if (empty($goUser) || is_null($goUser)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: Session User Not Defined."
		);
	} elseif (empty($userid) || is_null($userid)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: Sender User ID not Defined."
		);
	} elseif (empty($to_user_id) || is_null($to_user_id)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Receiver User ID not Defined."
                );
        } else {
		$dataUsers = array(
			"chat_current_session"		=> $to_user_id
		);

		$updateUsers = $goDB->where('id', $userid)
			->update('users', $dataUsers);

		if($chat_action == "update_user_chat"){
			$dataChat = array(
				"loaded" => '1' 
			);
		} else {
			$dataChat = array(
				"status"		=> '0',
				"loaded"		=> '1',
			);
		}

		$updateChat = $goDB->where('sender_userid', $to_user_id)
			->where('reciever_userid', $userid)
			->where('status', '1')
			->update('go_agent_chat', $dataChat);

		if($updateUsers && $updateChat) {
			$apiresults = array(
				"result" => "success"
			);
		} else {
			$apiresults = array(
				"result" => "error",
				"error" => $goDB->getLastError()
			);
		}
	}
	return $apiresults;

	
?>

