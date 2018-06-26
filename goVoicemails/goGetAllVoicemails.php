<?php
 /**
 * @file 		goGetAllVoicemails.php
 * @brief 		API for Voicemails
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Abenoja
 * @author     	Chris Lomuntad
 * @author		Jeremiah Sebastian Samatra
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
 
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	

	if (checkIfTenant($log_group, $goDB)) {
		$astDB->where('user_group', $log_group);
	}

	//$query = "SELECT voicemail_id,fullname,active,messages,old_messages,delete_vm_after_email,user_group FROM vicidial_voicemail $ul $addedSQL ORDER BY voicemail_id;";
	$cols = array("voicemail_id", "fullname", "active", "messages", "old_messages", "delete_vm_after_email", "user_group");
	$astDB->orderBy("voicemail_id", "desc");
	$rsltv = $astDB->get("vicidial_voicemail", NULL, $cols);
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
