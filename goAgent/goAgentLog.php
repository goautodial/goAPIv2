<?php
 /**
 * @file 		goAgentLog.php
 * @brief 		API to log agent activities
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

if (isset($_GET['goEvent'])) { $event = $astDB->escape($_GET['goEvent']); }
    else if (isset($_POST['goEvent'])) { $event = $astDB->escape($_POST['goEvent']); }
if (isset($_GET['goUserGroup'])) { $user_group = $astDB->escape($_GET['goUserGroup']); }
    else if (isset($_POST['goUserGroup'])) { $user_group = $astDB->escape($_POST['goUserGroup']); }
if (isset($_GET['goSessionID'])) { $session_id = $astDB->escape($_GET['goSessionID']); }
    else if (isset($_POST['goSessionID'])) { $session_id = $astDB->escape($_POST['goSessionID']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goComputerIP'])) { $computer_ip = $astDB->escape($_GET['goComputerIP']); }
    else if (isset($_POST['goComputerIP'])) { $computer_ip = $astDB->escape($_POST['goComputerIP']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }

$NOW = date("Y-m-d H:i:s");
$NOWepoch = date("U");

//$check = "SELECT user_log_id,event FROM vicidial_user_log WHERE user='$goUser' AND campaign_id='$campaign' ORDER BY user_log_id DESC LIMIT 1;";
$astDB->where('user', $goUser);
$astDB->where('campaign_id', $campaign);
$astDB->orderBy('user_log_id', 'desc');
$check_query = $astDB->getOne('vicidial_user_log', 'user_log_id,event');
$eventDB = $check_query['event'];

if( (strtoupper($event) === strtoupper($eventDB) /*&& strtoupper($eventDB) !== "MANUAL" */) || (strtoupper($event) === strtoupper("resume") && strtoupper($eventDB) === strtoupper("LOGIN") ) || (strtoupper($event) === strtoupper("resume") && strtoupper($eventDB) === strtoupper("MANUAL") ) ){
	//error DO NOT INSERT
	$APIResult = array("result" => "error");
}else{
	$insertData = array(
		"user" => $goUser,
		"event" => strtoupper($event),
		"campaign_id" => $campaign,
		"event_date" => $NOW,
		"event_epoch" => $NOWepoch,
		"user_group" => $user_group,
		"session_id" => $session_id,
		"server_ip" => $server_ip,
		"extension" => $extension,
		"computer_ip" => $computer_ip
	);
	$astDB->insert('vicidial_user_log', $insertData);
	$APIResult = array("result" => "success");
}
?>