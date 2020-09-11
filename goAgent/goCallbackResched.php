<?php
 /**
 * @file 		goCallbackResched.php
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
    if (isset($_GET['goCallbackDate'])) { $callback_date = $astDB->escape($_GET['goCallbackDate']); }
        else if (isset($_POST['goCallbackDate'])) { $callback_date = $astDB->escape($_POST['goCallbackDate']); }
    if (isset($_GET['goCallbackComment'])) { $callback_comments = $astDB->escape($_GET['goCallbackComment']); }
        else if (isset($_POST['goCallbackComment'])) { $callback_comments = $astDB->escape($_POST['goCallbackComment']); }
    if (isset($_GET['goCallbackOnly'])) { $callback_only = $astDB->escape($_GET['goCallbackOnly']); }
        else if (isset($_POST['goCallbackOnly'])) { $callback_only = $astDB->escape($_POST['goCallbackOnly']); }
	
	$astDB->where('callback_id', $callback_id);
	$rslt = $astDB->getOne('vicidial_callbacks', 'lead_id');
	$lead_id = $rslt['lead_id'];
	$cbExist = $astDB->getRowCount();


	if ($cbExist < 1) {
		$APIResult = array( "result" => "error", "message" => "Callback ID does NOT exist." );
	} else {
		if (!isset($callback_date) || strlen($callback_date) < 10) {
			$callback_date = date("Y-m-d H:i:s", strtotime('+6 hours'));
		}
		$callback_only = ($callback_only ? 'USERONLY' : 'ANYONE');
		$user_only = ($callback_only === 'USERONLY' ? $user : '');
		$updateData = array(
			'status' => 'ACTIVE',
			'callback_time' => $callback_date,
			'user' => $user_only,
			'recipient' => $callback_only,
			'comments' => $callback_comments
		);
		$astDB->where('callback_id', $callback_id);
		$rslt = $astDB->update('vicidial_callbacks', $updateData);
		
		// Check Callback Lists
		$updateData = array(
			'callback_time' => $callback_date,
			'seen' => false
		);
		$goDB->where('callback_id', $callback_id);
		$rslt = $goDB->update('go_callback_lists', $updateData);
		
		// Add Callback to events
		$CB30minsEarly = date("Y-m-d H:i:s", strtotime("-30 minutes", strtotime($callback_date)));
		$cbtime = date("h:i A", strtotime($callback_date));
		$astDB->where('lead_id', $lead_id);
		$rslt = $astDB->getOne('vicidial_list', 'phone_number');
		$insertData = array(
			'user_id' => $agent->user_id,
			'title' => "CALLBACK -- Call ".$rslt['phone_number']." around ".$cbtime,
			'description' => $callback_comments,
			'all_day' => 0,
			'start_date' => $CB30minsEarly,
			'end_date' => $callback_date,
			'url' => '',
			'alarm' => '',
			'notification_sent' => 0,
			'color' => '#03a9f4'
		);
		$rslt = $goDB->insert('events', $insertData);

		$APIResult = array( "result" => "success", "message" => "Callback Lead re-scheduled in {$callback_date}." );
	}
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
