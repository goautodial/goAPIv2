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
    
    // POST or GET Variables
    $extension 											= $astDB->escape($_REQUEST['extension']);
        
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
	} elseif (empty($extension) || is_null($extension)) { 
		$apiresults										= array(
			"result" 										=> "Error: Phone Extension Not Defined."
		); 
	} else {        
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
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
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
