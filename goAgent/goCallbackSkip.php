<?php
 /**
 * @file 		goCallbackSkip.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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

$agent = get_settings('user', $astDB, $goUser);

$user = $agent->user;
$user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($kamDB, $phone_login, $SIPserver);

if ($sipIsLoggedIn) {
    if (isset($_GET['goCallbackID'])) { $callback_id = $astDB->escape($_GET['goCallbackID']); }
        else if (isset($_POST['goCallbackID'])) { $callback_id = $astDB->escape($_POST['goCallbackID']); }
	
	$astDB->where('callback_id', $callback_id);
	$rslt = $astDB->getOne('vicidial_callbacks', 'lead_id');
	$lead_id = $rslt['lead_id'];
	$cbExist = $astDB->getRowCount();


	if ($cbExist < 1) {
		$APIResult = array( "result" => "error", "message" => "Callback ID does NOT exist." );
	} else {
		//$updateData = array(
		//	'status' => 'INACTIVE',
		//	'user' => '',
		//	'recipient' => 'ANYONE'
		//);
		//$astDB->where('callback_id', $callback_id);
		//$rslt = $astDB->update('vicidial_callbacks', $updateData);
		//
		//$updateData = array(
		//	'called_since_last_reset' => 'N',
		//	'status' => 'NEW'
		//);
		//$astDB->where('lead_id', $lead_id);
		//$rslt = $astDB->update('vicidial_list', $updateData);
		
		$callback_time = date("Y-m-d H:i:s", strtotime('+6 hours'));
		$updateData = array(
			'status' => 'ACTIVE',
			'callback_time' => $callback_time,
			'user' => $user,
			'recipient' => 'USERONLY'
		);
		$astDB->where('callback_id', $callback_id);
		$rslt = $astDB->update('vicidial_callbacks', $updateData);
		
		// Add Callback to events
		$CB30minsEarly = date("Y-m-d H:i:s", strtotime("-30 minutes", strtotime($callback_time)));
		$cbtime = date("h:i A", strtotime($callback_time));
		$astDB->where('lead_id', $lead_id);
		$rslt = $astDB->getOne('vicidial_list', 'phone_number');
		$insertData = array(
			'user_id' => $agent->user_id,
			'title' => "CALLBACK -- Call ".$rslt['phone_number']." around ".$cbtime,
			'description' => '',
			'all_day' => 0,
			'start_date' => $CB30minsEarly,
			'end_date' => $callback_time,
			'url' => '',
			'alarm' => '',
			'notification_sent' => 0,
			'color' => '#03a9f4'
		);
		$rslt = $goDB->insert('events', $insertData);

		//$APIResult = array( "result" => "success", "message" => "Callback Lead reverted back to queue as NEW." );
		$APIResult = array( "result" => "success", "message" => "Callback Lead re-scheduled in 6 hours." );
	}
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
