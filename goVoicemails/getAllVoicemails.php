<?php
 /**
 * @file 		getAllVoicemails.php
 * @brief 		API for Voicemails
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Alexander Abenoja  <alex@goautodial.com>
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

/*
$voicemail_id = $_REQUEST["voicemail_id"];
$active = $_REQUEST["active"];
$pass = $_REQUEST["pass"];
$fullname = $_REQUEST["fullname"];
$messages = $_REQUEST["messages"];
$old_messages = $_REQUEST["old_messages"];
$emial = $_REQUEST["email"];
$delete_vm_after_email = $_REQUEST["delete_vm_after_email"];
$voicemail_timezone = $_REQUEST["voicemail_timezone"];
$voicemail_options = $_REQUEST["voicemail_options"];
$user_group = $_REQUEST["user_group"];
*/
### voicemail_id, active enum('N','Y'), pass, fullname, messages, old_messages, email, delete_vm_after_email enum('N','Y'), voicemail_timezone, voicemail_options, user_group, voicemail_greeting

$groupId = go_get_groupid($goUser, $astDB);

if (!checkIfTenant($groupId, $goDB)) {
	//$ul = "";
} else {
	//$ul = "AND user_group='$groupId'";
	//$addedSQL = "WHERE user_group='$groupId'";
	$astDB->where('user_group', $groupId);
}

//$query = "SELECT voicemail_id,fullname,active,messages,old_messages,delete_vm_after_email,user_group FROM vicidial_voicemail $ul $addedSQL ORDER BY voicemail_id;";
$astDB->orderBy('voicemail_id', 'desc');
$rsltv = $astDB->get('vicidial_voicemail', null, 'voicemail_id,fullname,active,messages,old_messages,delete_vm_after_email,user_group');
$countRsltv = $astDB->getRowCount();
	
if($countRsltv > 0) {
	foreach ($rsltv as $fresults){
		$dataVoicemailID[] = $fresults['voicemail_id'];
		$dataFullname[] = $fresults['fullname'];
		$dataActive[] = $fresults['active'];
		$dataMessages[] = $fresults['messages'];
		$dataOldMessages[] = $fresults['old_messages'];
		$dataDeleteVMAfterEmail[] = $fresults['delete_vm_after_email'];
		$dataUserGroup[] = $fresults['user_group'];
		$apiresults = array("result" => "success", "voicemail_id" => $dataVoicemailID, "fullname" => $dataFullname, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "delete_vm_after_email" => $dataDeleteVMAfterEmail, "user_group" => $dataUserGroup);
	}
} else {
	$apiresults = array("result" => "Empty");
}

?>
