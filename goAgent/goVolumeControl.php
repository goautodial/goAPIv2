<?php
 /**
 * @file 		goVolumeControl.php
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

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goChannel'])) { $channel = $astDB->escape($_GET['goChannel']); }
    else if (isset($_POST['goChannel'])) { $channel = $astDB->escape($_POST['goChannel']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goQueryCID'])) { $queryCID = $astDB->escape($_GET['goQueryCID']); }
    else if (isset($_POST['goQueryCID'])) { $queryCID = $astDB->escape($_POST['goQueryCID']); }

$server_ip = (strlen($server_ip) > 0) ? $server_ip : $phone_settings->server_ip;

if ( (strlen($exten) < 1) || (strlen($channel) < 1) || (strlen($stage) < 1) || (strlen($queryCID) < 1) ) {
    $APIResult = array( "result" => "error", "message" => "Either conference, stage or queryCID is not valid, Originate command not inserted." );
} else {
	$participant_number = 'XXYYXXYYXXYYXX';
	if (preg_match('/UP/i',$stage)) {$vol_prefix = '4';}
	if (preg_match('/DOWN/i',$stage)) {$vol_prefix = '3';}
	if (preg_match('/UNMUTE/i',$stage)) {$vol_prefix = '2';}
	if (preg_match('/MUTING/i',$stage)) {$vol_prefix = '1';}
	
	$local_DEF = 'Local/';
	$local_AMP = '@';

	$volume_local_channel = "$local_DEF$participant_number$vol_prefix$exten$local_AMP$ext_context";

	//$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $volume_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$exten');";
	$insertData = array(
		'man_id' => '',
		'uniqueid' => '',
		'entry_date' => $NOW_TIME,
		'status' => 'NEW',
		'response' => 'N',
		'server_ip' => $server_ip,
		'channel' => '',
		'action' => 'Originate',
		'callerid' => $queryCID,
		'cmd_line_b' => "Channel: $volume_local_channel",
		'cmd_line_c' => "Context: $ext_context",
		'cmd_line_d' => "Exten: 8300",
		'cmd_line_e' => 'Priority: 1',
		'cmd_line_f' => "Callerid: $queryCID",
		'cmd_line_g' => '',
		'cmd_line_h' => '',
		'cmd_line_i' => '',
		'cmd_line_j' => $channel,
		'cmd_line_k' => $exten
	);
	$astDB->insert('vicidial_manager', $insertData);
	
    $APIResult = array( "result" => "success", "message" => "Volume command sent for Conference $exten, Stage $stage Channel $channel on $server_ip." );
}
?>