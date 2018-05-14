<?php
 /**
 * @file 		getVoicemailInfo.php
 * @brief 		API for Voicemails
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

$voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);
   
if($voicemail_id == null) {
    $apiresults = array("result" => "Error: Set a value for Voicemail ID.");
} else {
    $groupId = go_get_groupid($goUser, $astDB);

	if (!checkIfTenant($groupId, $goDB)) {
		$ul = "";
	} else {
		//$ul = "AND user_group='$groupId'";
		//$addedSQL = "AND user_group='$groupId'";
		//$addedSQL = "";
		$astDB->where('user_group', $groupId);
	}

	//$query = "SELECT voicemail_id,pass,fullname,email,active,messages,old_messages,delete_vm_after_email,user_group FROM vicidial_voicemail WHERE voicemail_id='$voicemail_id' $ul ORDER BY voicemail_id LIMIT 1;";
	$astDB->where('voicemail_id', $voicemail_id);
	$astDB->orderBy('voicemail_id', 'desc');
	$rsltv = $astDB->getOne('vicidial_voicemail', 'voicemail_id,pass,fullname,email,active,messages,old_messages,delete_vm_after_email,user_group');
	$exist = $astDB->getRowCount();

	if($exist > 0) {
		foreach ($rsltv as $fresults){									
			$dataVoicemailID[] = $fresults['voicemail_id'];
			$dataPassword[] = $fresults['pass'];
			$dataFullname[] = $fresults['fullname'];
			$dataEmail[] = $fresults['email'];
			$dataActive[] = $fresults['active'];
			$dataMessages[] = $fresults['messages'];
			$dataOldMessages[] = $fresults['old_messages'];
			$dataDeleteVMAfterEmail[] = $fresults['delete_vm_after_email'];
			$dataUserGroup[] = $fresults['user_group'];
			$apiresults = array("result" => "success", "voicemail_id" => $dataVoicemailID, "password"=> $dataPassword,"fullname" => $dataFullname, "email" => $dataEmail, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "delete_vm_after_email" => $dataDeleteVMAfterEmail, "user_group" => $dataUserGroup);
		}
	} else {
		$apiresults = array("result" => "Error: Lead Filter does not exist.");
	}
}
?>
