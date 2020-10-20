<?php
/**
 * @file 		goGetWhatsappChatUsers.php
 * @brief 		API to get users list to chat
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

	$userid 										= $goDB->escape($_REQUEST['userid']);
	  
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
			"u.userid",
            		"name",
		        "avatar",
            		"fullname",
            		"chat_current_session",
			"chat_online"
		);
		
		$results = $goDB->where("u.userid != $userid")
			->groupBy('u.userid')
			->get('users u', NULL, $cols);		
		$query = $goDB->getLastQuery();

		if ($goDB->count > 0) {

		$unread_results = $goDB->where("u.userid != $userid")
                        ->where('c.status', '1')
                        ->where('c.reciever_userid', $userid)
                        ->join('go_agent_chat c', 'u.userid = c.sender_userid', 'LEFT')
                        ->groupBy('u.userid')
                        ->get('users u', 1000, "u.userid, count('chatid') as unread_count");

		foreach($results as $result){
        	        $dataUserid[] = $result['userid'];
                	$dataAvatar[] = $result['avatar'];
	                $dataName[] = $result['name'];
        	        $dataFullName[] = $result['fullname'];
                	$dataPhone[] = $result['phone'];
	                $dataCurrentSession[] = $result['chat_current_session'];
	                $dataOnline[] = $result['chat_online'];
		    foreach($unread_results as $unread){
			if($result['userid'] == $unread['userid']){
				$count = $unread['unread_count'];
				break;
			} else {
				$count = 0;
			}
		    }
			if(!isset($count)){
				$count = 0;
			}
			$dataCount[] = $count;
	        }
		
		    $apiresults 							= array(
				"result" 								=> "success", 
                		"userid" 							    => $dataUserid,
		                "username" 							    => $dataFullName,
		                "avatar" 							    => $dataAvatar,
		                "current_session" 					    => $dataCurrentSession,
		                "online" 							    => $dataOnline,
				"count"									=> $dataCount
	            );
            
		} else {
			$apiresults                             = array(
				"result"								=> "No data available in table",
				"query" => $goDB->getLastError()
			);
		}
	}
	
?>

