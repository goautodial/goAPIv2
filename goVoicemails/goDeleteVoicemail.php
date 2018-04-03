<?php
 /**
 * @file 		goDeleteVoicemail.php
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

### POST or GET Variables
$voicemail = $astDB->escape($_REQUEST['voicemail_id']);
$voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);
$ip_address = $astDB->escape($_REQUEST['hostname']);

$log_user = $astDB->escape($_REQUEST['log_user']);
$log_group = $astDB->escape($_REQUEST['log_group']);

### Check Voicemail ID if its null or empty
if($voicemail_id == null) { 
	$apiresults = array("result" => "Error: Set a value for Voicemail ID."); 
} else {
	//$query = "SELECT active_voicemail_server from system_settings";
	$resultIP = $astDB->get('system_settings', null, 'active_voicemail_server');
	foreach ($resultIP as $fresults){
		$server_ip = $astDB->escape($fresults['active_voicemail_server']);
		if($server_ip != null){$ip_address = $server_ip; }
	}
	
	$groupId = go_get_groupid($goUser, $astDB);
	
	if (!checkIfTenant($groupId, $goDB)) {
		//$ul = "";
	} else {
		//$ul = "AND user_group='$groupId'";
		//$addedSQL = "WHERE user_group='$groupId'";
		$astDB->where('user_group', $groupId);
	}
	
	//$queryOne = "SELECT voicemail_id FROM vicidial_voicemail $ul where voicemail_id='$voicemail';";
	$astDB->where('voicemail_id', $voicemail);
	$rsltvOne = $astDB->get('vicidial_voicemail');
	$countResult = $astDB->getRowCount();

	if($countResult > 0) {
		//$deleteQuery = "DELETE FROM vicidial_voicemail WHERE voicemail_id = '$voicemail';";
		$astDB->where('voicemail_id', $voicemail);
		$deleteResult = $astDB->delete('vicidial_voicemail');
		//echo $deleteQuery;
		
		$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Voicemail ID: $voicemail", $log_group, $deleteQuery);
		
		$apiresults = array("result" => "success");
	} else {
		$apiresults = array("result" => "Error: Voicemail doesn't exist.");
	}
}//end
?>