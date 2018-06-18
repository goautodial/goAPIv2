<?php
/**
 * @file        goGetAllPhones.php
 * @brief       API for get get all Phone Details
 * @copyright   Copyright (C) GOautodial Inc.
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
    
    $limit = $astDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 1000; } else { $limit = $limit; }

	if(empty($session_user)){
		$apiresults = array("result" => "Error: Session User Not Defined.");
	}else{
		
		// generate random phone login
		$x = 0;
		$y = 0;
		$phone_login = '';
		while($x == $y){
			$random_digit = mt_rand(1000000000, 9999999999);
			$astDB->where("phone_login", $random_digit);
			$astDB->getOne("vicidial_users", "phone_login");
			//$check_existing_phonelogins_query = "SELECT phone_login FROM vicidial_users WHERE phone_login = '$random_digit';";
			
			if($astDB->count < 1){
				$y = 1;
				$phone_login = $random_digit;
			}
		}
	   	
		$groupId = go_get_groupid($session_user, $astDB);
		
		if (!checkIfTenant($groupId, $astDB)) {
			$ul='';
		} else {
			if ($groupId != 'ADMIN') {
				$astDB->where("user_group", $groupId);
				//$ul = "WHERE user_group='$groupId'";
			}
		}
		$col = Array("extension", "protocol", "server_ip", "active", "messages", "old_messages");
	   	$getQuery = $astDB->get("phones", $limit, $col);
		//$query = "SELECT extension, protocol, server_ip, active, messages, old_messages FROM phones $ul ORDER BY extension LIMIT $limit;";
		
		foreach ($getQuery as $fresults){
			$dataExtension[] = $fresults['extension'];
			$dataProtocol[] = $fresults['protocol'];
			$dataServerIp[] = $fresults['server_ip'];
			$dataActive[] = $fresults['active'];
			$dataMessages[] = $fresults['messages'];
			$dataOldMessages[] = $fresults['old_messages'];
		}
		
		$apiresults = array("result" => "success", "extension" => $dataExtension, "protocol" => $dataProtocol, "server_ip" => $dataServerIp, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "available_phone" => $phone_login);
	}
		
?>
