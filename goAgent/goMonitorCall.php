<?php
 /**
 * @file 		goMonitorCall.php
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
$campaign_settings = get_settings('campaign', $astDB, $campaign);
$system_settings = get_settings('system', $astDB);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goTask'])) { $ACTION = $astDB->escape($_GET['goTask']); }
    else if (isset($_POST['goTask'])) { $ACTION = $astDB->escape($_POST['goTask']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goChannel'])) { $channel = $astDB->escape($_GET['goChannel']); }
    else if (isset($_POST['goChannel'])) { $channel = $astDB->escape($_POST['goChannel']); }
if (isset($_GET['goQueryCID'])) { $queryCID = $astDB->escape($_GET['goQueryCID']); }
    else if (isset($_POST['goQueryCID'])) { $queryCID = $astDB->escape($_POST['goQueryCID']); }
if (isset($_GET['goUniqueID'])) { $uniqueid = $astDB->escape($_GET['goUniqueID']); }
    else if (isset($_POST['goUniqueID'])) { $uniqueid = $astDB->escape($_POST['goUniqueID']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goFilename'])) { $filename = $astDB->escape($_GET['goFilename']); }
    else if (isset($_POST['goFilename'])) { $filename = $astDB->escape($_POST['goFilename']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goExtPriority'])) { $ext_priority = $astDB->escape($_GET['goExtPriority']); }
    else if (isset($_POST['goExtPriority'])) { $ext_priority = $astDB->escape($_POST['goExtPriority']); }
if (isset($_GET['goFromVDC'])) { $FROMvdc = $astDB->escape($_GET['goFromVDC']); }
    else if (isset($_POST['goFromVDC'])) { $FROMvdc = $astDB->escape($_POST['goFromVDC']); }
if (isset($_GET['goFormat'])) { $format = $astDB->escape($_GET['goFormat']); }
    else if (isset($_POST['goFormat'])) { $format = $astDB->escape($_POST['goFormat']); }
if (isset($_GET['goFromAPI'])) { $FROMapi = $astDB->escape($_GET['goFromAPI']); }
    else if (isset($_POST['goFromAPI'])) { $FROMapi = $astDB->escape($_POST['goFromAPI']); }

$user = $agent->user;
$server_ip = (strlen($server_ip) > 0) ? $server_ip : $phone_settings->server_ip;

$VDCL_ingroup_recording_override = '';
$VDCL_ingroup_rec_filename = '';
$Ctype = 'A';
$MT[0] = '';
$row = '';
$rowx = '';
$vidSQL = '';
$VDterm_reason = '';
$ext_priority = (!isset($ext_priority)) ? 1 : $ext_priority;
$FROMvdc = (!isset($FROMvdc)) ? 'YES' : $FROMvdc;

if ($is_logged_in) {
    if ( ($ACTION=="Monitor") || ($ACTION=="StopMonitor") ) {
		if ($ACTION=="StopMonitor")
			{$SQLfile = "";}
		else
			{$SQLfile = "File: $filename";}
	
		$row = '';
		$rowx = '';
		$channel_live = 1;
		if ( (strlen($channel) < 3) || (strlen($queryCID) < 15) || (strlen($filename) < 8) ) {
			$channel_live = 0;
			$APIResult = array( "result" => "error", "message" => "Either channel, queryCID or filename is NOT valid, $ACTION command not inserted" );
		} else {
			//$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$channel';";
			$astDB->where('server_ip', $server_ip);
			$astDB->where('channel', $channel);
			$rslt = $astDB->get('live_channels');
			$row_ct = $astDB->getRowCount();
			if ($row_ct == 0) {
				//$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$channel';";
				$astDB->where('server_ip', $server_ip);
				$astDB->where('channel', $channel);
				$rslt = $astDB->get('live_sip_channels');
				$rowx_ct = $astDB->getRowCount();
				if ($rowx_ct == 0) {
					$channel_live = 0;
					$APIResult = array( "result" => "error", "message" => "Channel $channel is not live on $server_ip, $ACTION command not inserted" );
				}
			}
			if ($channel_live == 1) {
				//$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','$ACTION','$queryCID','Channel: $channel','$SQLfile','','','','','','','','');";
				$insertData = array(
                    'man_id' => '',
                    'uniqueid' => '',
                    'entry_date' => $NOW_TIME,
                    'status' => 'NEW',
                    'response' => 'N',
                    'server_ip' => $server_ip,
                    'channel' => '',
                    'action' => $ACTION,
                    'callerid' => $queryCID,
                    'cmd_line_b' => "Channel: $channel",
                    'cmd_line_c' => "$SQLfile",
                    'cmd_line_d' => "",
                    'cmd_line_e' => '',
                    'cmd_line_f' => "",
                    'cmd_line_g' => "",
                    'cmd_line_h' => '',
                    'cmd_line_i' => '',
                    'cmd_line_j' => '',
                    'cmd_line_k' => ''
                );
				$rslt = $astDB->insert('vicidial_manager', $insertData);
	
				if ($ACTION == "Monitor") {
					//$stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtimE','$filename','$lead_id','$user')";
					$insertData = array(
						'channel' => $channel,
						'server_ip' => $server_ip,
						'extension' => $exten,
						'start_time' => $NOW_TIME,
						'start_epoch' => $StarTtimE,
						'filename' => $filename,
						'lead_id' => $lead_id,
						'user' => $user
					);
					$rslt = $astDB->insert('recording_log', $insertData);
	
					//$stmt="SELECT recording_id FROM recording_log where filename='$filename'";
					//$astDB->where('filename', $filename);
					//$rslt = $astDB->get('recording_log', null, 'recording_id');
					$recording_id = $astDB->getInsertId();
				} else {
					//$stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename'";
					$astDB->where('filename', $filename);
					$rslt = $astDB->get('recording_log', null, 'recording_id,start_epoch');
					$rec_count = $astDB->getRowCount();
					if ($rec_count>0) {
						$row = $rslt[0];
						$recording_id = $row['recording_id'];
						$start_time = $row['start_epoch'];
						$length_in_sec = ($StarTtimE - $start_time);
						$length_in_min = ($length_in_sec / 60);
						$length_in_min = sprintf("%8.2f", $length_in_min);
	
						//$stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtimE',length_in_sec=$length_in_sec,length_in_min='$length_in_min' where filename='$filename'";
						$updateData = array(
							'end_time' => $NOW_TIME,
							'end_epoch' => $StarTtimE,
							'length_in_sec' => $length_in_sec,
							'length_in_min' => $length_in_min
						);
						$astDB->where('filename', $filename);
						$rslt = $astDB->update('recording_log', $updateData);
					}
				}
				
				$APIResult = array( "result" => "success", "message" => "$ACTION command sent for Channel $channel on $server_ip", "filename" => $filename, "recording_id" => $recording_id );
			}
		}
	}

	######################
	# ACTION=MonitorConf or StopMonitorConf  - insert Monitor/StopMonitor Manager statement to start recording on a conference
	######################
	if ( ($ACTION == "MonitorConf") || ($ACTION == "StopMonitorConf") ) {
		$row='';
		$rowx='';
		$channel_live = 1;
		$uniqueidSQL = '';
	
		if ( (strlen($exten) < 3) || (strlen($channel) < 4) || (strlen($filename) < 8) ) {
			$channel_live = 0;
			$APIResult = array( "result" => "error", "message" => "Either the channel, exten or filename is NOT valid, $ACTION command not inserted" );
		} else {
			$VDvicidial_id = '';
	
			if ($ACTION=="MonitorConf") {
				//$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$filename','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $filename','','','','','');";
				$insertData = array(
                    'man_id' => '',
                    'uniqueid' => '',
                    'entry_date' => $NOW_TIME,
                    'status' => 'NEW',
                    'response' => 'N',
                    'server_ip' => $server_ip,
                    'channel' => '',
                    'action' => 'Originate',
                    'callerid' => $filename,
                    'cmd_line_b' => "Channel: $channel",
                    'cmd_line_c' => "Context: $ext_context",
                    'cmd_line_d' => "Exten: $exten",
                    'cmd_line_e' => "Priority: $ext_priority",
                    'cmd_line_f' => "Callerid: $filename",
                    'cmd_line_g' => "",
                    'cmd_line_h' => '',
                    'cmd_line_i' => '',
                    'cmd_line_j' => '',
                    'cmd_line_k' => ''
                );
				$rslt = $astDB->insert('vicidial_manager', $insertData);
	
				//$stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtimE','$filename','$lead_id','$user')";
				$insertData = array(
					'channel' => $channel,
					'server_ip' => $server_ip,
					'extension' => $exten,
					'start_time' => $NOW_TIME,
					'start_epoch' => $StarTtimE,
					'filename' => $filename,
					'lead_id' => $lead_id,
					'user' => $user
				);
				$rslt = $astDB->insert('recording_log', $insertData);
				$RLaffected_rows = $astDB->getRowCount();
				if ($RLaffected_rows > 0) {
					$recording_id = $astDB->getInsertId();
				}
	
				if ($FROMvdc == 'YES') {
					##### update vla record with recording_id
					//$stmt = "UPDATE vicidial_live_agents SET external_recording='$recording_id' where user='$user';";
					$astDB->where('user', $user);
					$rslt = $astDB->update('vicidial_live_agents', array('external_recording' => $recording_id));
	
					##### get call type from vicidial_live_agents table
					$VLA_inOUT = 'NONE';
					//$stmt="SELECT comments FROM vicidial_live_agents where user='$user' order by last_update_time desc limit 1;";
					$astDB->where('user', $user);
					$astDB->orderBy('last_update_time', 'desc');
					$rslt = $astDB->getOne('vicidial_live_agents', 'comments');
					$VLA_inOUT_ct = $astDB->getRowCount();
					if ($VLA_inOUT_ct > 0) {
						$row = $rslt;
						$VLA_inOUT = $row['comments'];
					}
					if ($VLA_inOUT == 'INBOUND') {
						$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
	
						##### look for the vicidial ID in the vicidial_closer_log table
						//$stmt="SELECT closecallid FROM vicidial_closer_log where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by closecallid desc limit 1;";
						$astDB->where('lead_id', $lead_id);
						$astDB->where('user', $user);
						$astDB->where('call_date', $four_hours_ago, '>');
						$astDB->orderBy('closercallid', 'desc');
						$rslt = $astDB->getOne('vicidial_closer_log', 'closercallid AS vicidial_id');
					} else {
						##### look for the vicidial ID in the vicidial_log table
						//$stmt="SELECT uniqueid FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id';";
						$astDB->where('lead_id', $lead_id);
						$astDB->where('uniqueid', $uniqueid);
						$rslt = $astDB->getOne('vicidial_log', 'uniqueid AS vicidial_id');
					}
					$VM_mancall_ct = $astDB->getRowCount();
					if ($VM_mancall_ct > 0) {
						$row = $rslt;
						$VDvicidial_id = $row['vicidial_id'];
	
						//$stmt = "UPDATE recording_log SET vicidial_id='$VDvicidial_id' where recording_id='$recording_id';";
						$astDB->where('recording_id', $recording_id);
						$rslt = $astDB->update('recording_log', array('vicidial_id' => $VDvicidial_id));
					}
				}
			}

		##### StopMonitorConf steps #####
			else {
				if ($uniqueid == 'IN') {
					$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
	
					### find the value to put in the vicidial_id field if this was an inbound call
					//$stmt="SELECT closecallid from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
					$astDB->where('lead_id', $lead_id);
					$astDB->where('call_date', $four_hours_ago, '>');
					$astDB->orderBy('call_date', 'desc');
					$rslt = $astDB->getOne('vicidial_closer_log', 'closercallid');
					$VAC_qm_ct = $astDB->getRowCount();
					if ($VAC_qm_ct > 0) {
						$row = $rslt;
						$uniqueidSQL	= $row['closercallid'];
					}
				} else {
					if (strlen($uniqueid) > 8) {
						$uniqueidSQL	= $uniqueid;
					}
				}
	
				if ($FROMvdc=='YES') {
					##### update vla recording record to blank
					//$stmt = "UPDATE vicidial_live_agents SET external_recording='' where user='$user';";
					$astDB->where('user', $user);
					$rslt = $astDB->update('vicidial_live_agents', array('external_recording' => ''));
				}
				
				//$stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename'";
				$astDB->where('filename', $filename);
				$rslt = $astDB->get('recording_log', null, 'recording_id,start_epoch');
				$rec_count = $astDB->getRowCount();
				if ($rec_count > 0) {
					$row = $rslt[0];
					$recording_id = $row['recording_id'];
					$start_time = $row['start_epoch'];
					$length_in_sec = ($StarTtimE - $start_time);
					$length_in_min = ($length_in_sec / 60);
					$length_in_min = sprintf("%8.2f", $length_in_min);
	
					//$stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtimE',length_in_sec=$length_in_sec,length_in_min='$length_in_min' $uniqueidSQL where filename='$filename'";
					$updateData = array(
						'end_time' => $NOW_TIME,
						'end_epoch' => $StarTtimE,
						'length_in_sec' => $length_in_sec,
						'length_in_min' => $length_in_min,
						'vicidial_id' => $uniqueidSQL
					);
					$astDB->where('filename', $filename);
					$rslt = $astDB->update('recording_log', $updateData);
				}
	
				# find and hang up all recordings going on in this conference # and extension = '$exten' 
				$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$channel%\" and (channel LIKE \"%,1\" or channel LIKE \"%;1\");";
				$rslt = $astDB->rawQuery($stmt);
			#	$rec_count = intval(mysql_num_rows($rslt) / 2);
				$rec_count = $astDB->getRowCount();
				$h = 0;
				while ($rec_count > $h) {
					$rowx = $rslt[$h];
					$HUchannel[$h] = $rowx['channel'];
					$h++;
				}
				$i = 0;
				while ($h > $i) {
					//$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtimE$i','Channel: $HUchannel[$i]','','','','','','','','','');";
					$insertData = array(
						'man_id' => '',
						'uniqueid' => '',
						'entry_date' => $NOW_TIME,
						'status' => 'NEW',
						'response' => 'N',
						'server_ip' => $server_ip,
						'channel' => '',
						'action' => 'Hangup',
						'callerid' => "RH12345$StarTtimE$i",
						'cmd_line_b' => "Channel: $HUchannel[$i]",
						'cmd_line_c' => "",
						'cmd_line_d' => "",
						'cmd_line_e' => "",
						'cmd_line_f' => "",
						'cmd_line_g' => "",
						'cmd_line_h' => '',
						'cmd_line_i' => '',
						'cmd_line_j' => '',
						'cmd_line_k' => ''
					);
					$rslt = $astDB->insert('vicidial_manager', $insertData);
					$i++;
				}
			}
			
			$APIResult = array( "result" => "success", "message" => "$ACTION command sent for Channel $channel on $server_ip", "filename" => $filename, "recording_id" => $recording_id, "rec_message" => "RECORDING WILL LAST UP TO 60 MINUTES" );
		}
	}
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>