<?php
 /**
 * @file        goGetphoneInfo.php
 * @brief       API for get specific Phone Details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
    
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB);    
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	
    // POST or GET Variables
	$extension 										= $astDB->escape($_REQUEST['extension']);	
        
    // Error Checking
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($extension) || is_null($extension)) { 
		$apiresults									= array(
			"result" 									=> "Error: Set a value for EXTEN ID."
		); 
	} else {        
        if (checkIfTenant($log_group, $astDB)) {
        	$astDB->where("user_group", $log_group); 
    	}
		
		$cols 										= array(
			"extension",
			"protocol",
			"server_ip",
			"dialplan_number",
			"voicemail_id",
			"status",
			"active",
			"fullname",
			"messages",
			"old_messages",
			"user_group"
		);
		
		$astDB->where("extension", $extension);
        $astDB->orderby("extension", "asc");
        $fresults 									= $astDB->getOne("phones", $cols);

		if ($astDB->count > 0) {
            $dataExtension 							= $fresults['extension'];
            $dataProtocol 							= $fresults['protocol'];
            $dataServerIp 							= $fresults['server_ip'];
            $dataDialplanNumber 					= $fresults['dialplan_number'];
            $dataVoicemailId 						= $fresults['voicemail_id'];
            $dataStatus 							= $fresults['status'];
            $dataActive 							= $fresults['active'];
            $dataFullname 							= $fresults['fullname'];
            $dataMessages 							= $fresults['messages'];
            $dataOldMessages 						= $fresults['old_messages'];
            $dataUserGroup 							= $fresults['user_group'];

            $apiresults 							= array(
				"result" 								=> "success", 
				"extension" 							=> $dataExtension, 
				"protocol" 								=> $dataProtocol, 
				"server_ip" 							=> $dataServerIp, 
				"dialplan_number" 						=> $dataDialplanNumber, 
				"voicemail_id" 							=> $dataVoicemailId, 
				"status" 								=> $dataStatus, 
				"active" 								=> $dataActive, 
				"fullname" 								=> $dataFullname, 
				"messages" 								=> $dataMessages, 
				"old_messages" 							=> $dataOldMessages, 
				"user_group" 							=> $dataUserGroup
			);
			
			$log_id 								= log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the info of Phone: $exten_id", $log_group);
		} else {
			$apiresults 							= array(
				"result" 								=> "Error: Phone doesn't exist."
			);
		}
	}
?>
