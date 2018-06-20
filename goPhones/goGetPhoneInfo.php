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
	$extension = $astDB->escape($_REQUEST['extension']);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
        
    // Check extension if its null or empty
    if(empty($session_user)){
        $apiresults = array("result" => "Error: Session User Not Defined.");
    }elseif(is_null($extension)) { 
		$apiresults = array("result" => "Error: Set a value for EXTEN ID."); 
	} else {
        $log_user = $session_user;
        $groupId = go_get_groupid($session_user, $astDB);
        
        if (!checkIfTenant($groupId, $astDB)) {
        	$astDB->where("extension", $extension);
            //$ul = "WHERE extension='$extension'";
    	} else {
            $astDB->where("extension", $extension);
            $astDB->where("user_group", $groupId); 
			//$ul = "WHERE extension='$extension' AND user_group='$groupId'";  
		}
        $astDB->orderby("extension", "asc");
        $fresults = $astDB->getOne("phones", "extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group");
   		//$query = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
		$countResult = $astDB->count;

		if($countResult > 0) {
            $dataExtension = $fresults['extension'];
            $dataProtocol = $fresults['protocol'];
            $dataServerIp = $fresults['server_ip'];
            $dataDialplanNumber = $fresults['dialplan_number'];
            $dataVoicemailId = $fresults['voicemail_id'];
            $dataStatus = $fresults['status'];
            $dataActive = $fresults['active'];
            $dataFullname = $fresults['fullname'];
            $dataMessages = $fresults['messages'];
            $dataOldMessages = $fresults['old_messages'];
            $dataUserGroup = $fresults['user_group'];

            $apiresults = array("result" => "success", "extension" => $dataExtension, "protocol" => $dataProtocol, "server_ip" => $dataServerIp, "dialplan_number" => $dataDialplanNumber, "voicemail_id" => $dataVoicemailId, "status" => $dataStatus, "active" => $dataActive, "fullname" => $dataFullname, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "user_group" => $dataUserGroup);
			
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the info of Phone: $exten_id", $groupId);
		} else {
			$apiresults = array("result" => "Error: Phone doesn't exist.");
		}
	}
?>
