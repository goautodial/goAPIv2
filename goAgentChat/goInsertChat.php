<?php
/**
 * @file 		goInsertChat.php
 * @brief 		API to Add Chat Message
 * @copyright 	Copyright (c) 2020 GOautodial Inc.
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
	//receiver_userid, user_id, chat_message
	
	$receiver_userid 									= $goDB->escape($_REQUEST['to_user_id']);
	$sender_userid 										= $goDB->escape($_REQUEST['userid']);
	$chat_message 										= $goDB->escape($_REQUEST['chat_message']);
	$status = 1;
	$loaded = 0;

	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($receiver_userid) || is_null($receiver_userid)) {
		$apiresults                                     = array(
				"result"                                	=> "Error: Receiver ID not Defined."
		); 
	} elseif (empty($sender_userid) || is_null($sender_userid)) {
		$apiresults                                     = array(
				"result"                                	=> "Error: User ID not Defined."
		); 
	} else {
		$data						= array(
			'reciever_userid' 						=> $receiver_userid,
			'sender_userid' 						=> $sender_userid,
			'message' 							=> $chat_message,
			'loaded'							=> $loaded,
			'status'							=> $status
		);
		
		$insertdata 					= $goDB->insert('go_agent_chat', $data);					
		if($insertdata){
			$apiresults = array (
				"result"		=> 'success',
				"data"			=> $data
			);
		} else {
			$apiresults = array (
				"result"		=> $goDB->getLastError()
			);
		}			
	}
	return $apiresults;
?>

