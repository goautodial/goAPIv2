<?php
/**
 * @file 		goGetUserChat.php
 * @brief 		API to get user's chat
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

	$to_user_id 									= $goDB->escape($_REQUEST['to_user_id']);
	$from_user_id 									= $goDB->escape($_REQUEST['userid']);
	$action										= $goDB->escape($_REQUEST['action']);

	// Error Checking
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
	} else {
						
		$cols 										= array(
			"chatid",
			"fullname as username",
			"sender_userid",
			"reciever_userid",
			"message",
			"timestamp",
			"loaded",
			"c.status"
		);
		if($action == 'update_user_chat'){
			$goDB->where("(sender_userid = '$to_user_id' AND loaded = '0' AND c.status = '1')"); 
		}
	
		$result	= $goDB->orderBy('timestamp', 'asc')
			->where("((sender_userid = '$from_user_id' AND reciever_userid = '$to_user_id') OR (sender_userid = '$to_user_id' AND reciever_userid = '$from_user_id'))")
			->join("users", "sender_userid = userid", "LEFT")
			->get('go_agent_chat c', null, $cols);		
		
		if ($goDB->count > 0) {

		//$conversation = '<ul>';
		$previous_timestamp = "";
		$previous_username = "";
            	foreach($result as $chat){
                    /*if($chat["sender_userid"] == $from_user_id) {
			$conversation .= '<li class="sent">';
			//$conversation .= '<img width="22px" height="22px" src="userpics/'.$fromUserAvatar.'" alt="" />';
                    } else {
			$conversation .= '<li class="replies">';
			// $conversation .= '<img width="22px" height="22px" src="userpics/'.$toUserAvatar.'" alt="" />';
                    }
                    $conversation .= '<p>'.$chat["message"].'</p>';
                    $conversation .= '</li>';*/
		    $username = $chat['username'];
                    $message = $chat['message'];
                    $timestamp = strtotime($chat['timestamp']);
                    $timestamp = date('d M h:i A', $timestamp);
		    
		    if($timestamp == $previous_timestamp){
			$display_timestamp = "";
		    } else {
			$display_timestamp = $timestamp;
		    }

		    if($previous_username == $username){
			$display_username = "";
		    } else {
			$display_username = $username;
		    }

                    if($chat["sender_userid"] == $to_user_id) {
                        $conversation .= "<div class='direct-chat-msg'>";
                        $conversation .= "<div class='direct-chat-info clearfix'>";
                        $conversation .= "<span class='direct-chat-name pull-left'>$display_username</span>";
                        $conversation .= "<span class='direct-chat-timestamp pull-right'>$display_timestamp</span>";

                        $conversation .= "</div>";
                            
                        //$conversation .= "<img class='direct-chat-img' src='../dist/img/user1-128x128.jpg' alt='message user image'>";
                        $conversation .= "<div class='direct-chat-text'>";
                        $conversation .= "$message";
                        $conversation .= "</div>";
                        $conversation .= "</div>";
                    
                    } else {
                        $conversation .= "<div class='direct-chat-msg right'>";
                        $conversation .= "<div class='direct-chat-info clearfix'>";
                        $conversation .= "<span class='direct-chat-name pull-right'>$display_username</span>";
                        $conversation .= "<span class='direct-chat-timestamp pull-left'>$display_timestamp</span>";
                        $conversation .= "</div>";
                            
                        //$conversation .= "<img class='direct-chat-img' src='../dist/img/user1-128x128.jpg' alt='message user image'>";
                        $conversation .= "<div class='direct-chat-text'>";
                        $conversation .= "$message";
                        $conversation .= "</div>";
                        $conversation .= "</div>";
                    }

		    $previous_timestamp = $timestamp;
		    $previous_username = $username;

            	}
		    //$conversation .= '</ul>';	
		
		    $apiresults 						= array(
			"result" 							=> "success", 
			"conversation" 							=> $conversation,
			"query" => $goDB->getLastQuery()
		    );
		} else {
			$apiresults                             = array(
				"result"								=> "No data available in table",
				"query" => $goDB->getLastQuery()
			);
		}
	}
?>

