<?php
 /**
 * @file 		goXFERSendRedirect.php
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
$user = $agent->user;
$user_group = $agent->user_group;

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goChannel'])) { $channel = $astDB->escape($_GET['goChannel']); }
    else if (isset($_POST['goChannel'])) { $channel = $astDB->escape($_POST['goChannel']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }
if (isset($_GET['goExtenName'])) { $extenName = $astDB->escape($_GET['goExtenName']); }
    else if (isset($_POST['goExtenName'])) { $extenName = $astDB->escape($_POST['goExtenName']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goExtPriority'])) { $ext_priority = $astDB->escape($_GET['goExtPriority']); }
    else if (isset($_POST['goExtPriority'])) { $ext_priority = $astDB->escape($_POST['goExtPriority']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }
if (isset($_GET['goUniqueID'])) { $uniqueid = $astDB->escape($_GET['goUniqueID']); }
    else if (isset($_POST['goUniqueID'])) { $uniqueid = $astDB->escape($_POST['goUniqueID']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goSeconds'])) { $seconds = $astDB->escape($_GET['goSeconds']); }
    else if (isset($_POST['goSeconds'])) { $seconds = $astDB->escape($_POST['goSeconds']); }
if (isset($_GET['goSessionID'])) { $session_id = $astDB->escape($_GET['goSessionID']); }
    else if (isset($_POST['goSessionID'])) { $session_id = $astDB->escape($_POST['goSessionID']); }
if (isset($_GET['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_GET['goNoDeleteVDAC']); }
    else if (isset($_POST['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_POST['goNoDeleteVDAC']); }
if (isset($_GET['goTask'])) { $ACTION = $astDB->escape($_GET['goTask']); }
    else if (isset($_POST['goTask'])) { $ACTION = $astDB->escape($_POST['goTask']); }
if (isset($_GET['goCallServerIP'])) { $call_server_ip = $astDB->escape($_GET['goCallServerIP']); }
    else if (isset($_POST['goCallServerIP'])) { $call_server_ip = $astDB->escape($_POST['goCallServerIP']); }
if (isset($_GET['goQueryCID'])) { $queryCID = $astDB->escape($_GET['goQueryCID']); }
    else if (isset($_POST['goQueryCID'])) { $queryCID = $astDB->escape($_POST['goQueryCID']); }
if (isset($_GET['goExtraChannel'])) { $extrachannel = $astDB->escape($_GET['goExtraChannel']); }
    else if (isset($_POST['goExtraChannel'])) { $extrachannel = $astDB->escape($_POST['goExtraChannel']); }
if (isset($_GET['goPhoneCode'])) { $phone_code = $astDB->escape($_GET['goPhoneCode']); }
    else if (isset($_POST['goPhoneCode'])) { $phone_code = $astDB->escape($_POST['goPhoneCode']); }
if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
    else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
if (isset($_GET['goFilename'])) { $filename = $astDB->escape($_GET['goFilename']); }
    else if (isset($_POST['goFilename'])) { $filename = $astDB->escape($_POST['goFilename']); }
if (isset($_GET['goAgentChannel'])) { $agentchannel = $astDB->escape($_GET['goAgentChannel']); }
    else if (isset($_POST['goAgentChannel'])) { $agentchannel = $astDB->escape($_POST['goAgentChannel']); }
if (isset($_GET['goProtocol'])) { $protocol = $astDB->escape($_GET['goProtocol']); }
    else if (isset($_POST['goProtocol'])) { $protocol = $astDB->escape($_POST['goProtocol']); }
if (isset($_GET['goParkedBy'])) { $parkedby = $astDB->escape($_GET['goParkedBy']); }
    else if (isset($_POST['goParkedBy'])) { $parkedby = $astDB->escape($_POST['goParkedBy']); }
if (isset($_GET['goPresetName'])) { $preset_name = $astDB->escape($_GET['goPresetName']); }
    else if (isset($_POST['goPresetName'])) { $preset_name = $astDB->escape($_POST['goPresetName']); }
if (isset($_GET['goCallCID'])) { $CallCID = $astDB->escape($_GET['goCallCID']); }
    else if (isset($_POST['goCallCID'])) { $CallCID = $astDB->escape($_POST['goCallCID']); }

$NOWnum = date("YmdHis");

if ($is_logged_in) {
    if ($ACTION == "RedirectVD") {
        if ( (strlen($channel) < 3) or (strlen($queryCID) < 15) or (strlen($exten) < 1) or (strlen($campaign) < 1) or (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) or (strlen($uniqueid) < 2) or (strlen($lead_id) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            if (strlen($channel) < 3) {$message .= "'channel' must be greater than 2 characters\n";}
            if (strlen($queryCID) < 15) {$message .= "'queryCID' must be greater than 14 characters\n";}
            if (strlen($exten) < 1) {$message .= "'exten' must be set\n";}
            if (strlen($ext_context) < 1) {$message .= "'ext_context' must be set\n";}
            if (strlen($ext_priority) < 1) {$message .= "'ext_priority' must be set\n";}
            if (strlen($auto_dial_level) < 1) {$message .= "'auto_dial_level' must be set\n";}
            if (strlen($campaign) < 1) {$message .= "'campaign' must be set\n";}
            if (strlen($uniqueid) < 1) {$message .= "'uniqueid' must be set\n";}
            if (strlen($lead_id) < 1) {$message .= "'lead_id' must be set\n";}
            $message .= "\nRedirectVD Action not sent\n";
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            //$stmt = "select count(*) from vicidial_campaigns where campaign_id='$campaign' and campaign_allow_inbound='Y';";
            $astDB->where('campaign_id', $campaign);
            $astDB->where('campaign_allow_inbound', 'Y');
            $rslt = $astDB->get('vicidial_campaigns');
            $cai_cnt = $astDB->getRowCount();
            if ($cai_cnt > 0) {
                $four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4, date("i"), date("s"), date("m"), date("d"), date("Y")));
                //$stmt = "UPDATE vicidial_closer_log SET end_epoch='$StarTtimE', length_in_sec=(queue_seconds + $seconds), status='XFER' WHERE lead_id='$lead_id' AND call_date > \"$four_hours_ago\" ORDER BY start_epoch DESC LIMIT 1;";
                $rslt = $astDB->rawQuery("UPDATE vicidial_closer_log SET end_epoch='$StarTtimE', length_in_sec=(queue_seconds + $seconds), status='XFER' WHERE lead_id='$lead_id' AND call_date > \"$four_hours_ago\" ORDER BY start_epoch DESC LIMIT 1;");
            }
    
            //$stmt = "UPDATE vicidial_log set end_epoch='$StarTtimE', length_in_sec='$seconds',status='XFER' where uniqueid='$uniqueid';";
            $updateData = array(
                'end_epoch' => $StarTtimE,
                'length_in_sec' => $seconds,
                'status' => 'XFER'
            );
            $astDB->where('uniqueid', $uniqueid);
            $rslt = $astDB->update('vicidial_log', $updateData);
    
            if ($nodeletevdac < 1) {
                //$stmt = "DELETE from vicidial_auto_calls where uniqueid='$uniqueid';";
                $astDB->where('uniqueid', $uniqueid);
                $rslt = $astDB->delete('vicidial_auto_calls');
            }
    
            if (strlen($preset_name) > 0) {
                //$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,preset_name,campaign_id) values('$user','$NOW_TIME','BLIND_XFER','$server_ip','$exten','$channel','$lead_id','$preset_name','$campaign')";
                $insertData = array(
                    'user' => $user,
                    'call_date' => $NOW_TIME,
                    'call_type' => 'BLIND_XFER',
                    'server_ip' => $server_ip,
                    'phone_number' => $exten,
                    'number_dialed' => $channel,
                    'lead_id' => $lead_id,
                    'preset_name' => $preset_name,
                    'campaign_id' => $campaign
                );
                $rslt = $astDB->insert('user_call_log', $insertData);
    
                //$stmt = "SELECT count(*) from vicidial_xfer_stats where campaign_id='$campaign' and preset_name='$preset_name';";
                $astDB->where('campaign_id', $campaign);
                $astDB->where('preset_name', $preset_name);
                $rslt = $astDB->get('vicidial_xfer_stats');
                $xfer_cnt = $astDB->getRowCount();
                if ($xfer_cnt > 0) {
                    //$stmt = "UPDATE vicidial_xfer_stats SET xfer_count=(xfer_count+1) WHERE campaign_id='$campaign' AND preset_name='$preset_name';";
                    $rslt = $astDB->rawQuery("UPDATE vicidial_xfer_stats SET xfer_count=(xfer_count+1) WHERE campaign_id='$campaign' AND preset_name='$preset_name';");
                } else {
                    //$stmt = "INSERT INTO vicidial_xfer_stats SET campaign_id='$campaign',preset_name='$preset_name',xfer_count='1';";
                    $insertData = array(
                        'campaign_id' => $campaign,
                        'preset_name' => $preset_name,
                        'xfer_count' => 1
                    );
                    $rslt = $astDB->insert('vicidial_xfer_stats', $insertData);
                }
            }
    
            $ACTION = "Redirect";
        }
    }
    
    if ($ACTION == "RedirectToPark") {
        if ( (strlen($channel) < 3) or (strlen($queryCID) < 15) or (strlen($exten) < 1) or (strlen($extenName) < 1) or (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) or (strlen($parkedby) < 1) ) {
            $channel_live = 0;
            $message .= "One of these variables is not valid:\n";
            if (strlen($channel) < 3) {$message .= "'channel' must be greater than 2 characters\n";}
            if (strlen($queryCID) < 15) {$message .= "'queryCID' must be greater than 14 characters\n";}
            if (strlen($exten) < 1) {$message .= "'exten' must be set\n";}
            if (strlen($ext_context) < 1) {$message .= "'ext_context' must be set\n";}
            if (strlen($extenName) < 1) {$message .= "'extenName' must be set\n";}
            if (strlen($ext_priority) < 1) {$message .= "'ext_priority' must be set\n";}
            if (strlen($parkedby) < 1) {$message .= "'parkedby' must be set\n";}
            $message .= "\nRedirectToPark Action not sent\n";
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            //$stmt = "INSERT INTO parked_channels values('$channel','$server_ip','$CallCID','$extenName','$parkedby','$NOW_TIME');";
            $insertData = array(
                'channel' => $channel,
                'server_ip' => $server_ip,
                'channel_group' => $CallCID,
                'extension' => $extenName,
                'parked_by' => $parkedby,
                'parked_time' => $NOW_TIME
            );
            $rslt = $astDB->insert('parked_channels', $insertData);
            $ACTION = "Redirect";

            //$stmt = "INSERT INTO park_log SET uniqueid='$uniqueid',status='PARKED',channel='$channel',channel_group='$campaign',server_ip='$server_ip',parked_time='$NOW_TIME',parked_sec=0,extension='$CallCID',user='$user',lead_id='$lead_id';";
            $insertData = array(
                'uniqueid' => $uniqueid,
                'status' => 'PARKED',
                'channel' => $channel,
                'channel_group' => $campaign,
                'server_ip' => $server_ip,
                'parked_time' => $NOW_TIME,
                'parked_sec' => 0,
                'extension' => $CallCID,
                'user' => $user,
                'lead_id' => $lead_id
            );
            $rslt = $astDB->insert('park_log', $insertData);    
    
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
            $rslt = $astDB->get('system_settings', null, 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id');
            $qm_conf_ct = $astDB->getRowCount();
            $i = 0;
            while ($i < $qm_conf_ct) {
                $row = $rslt[$i];
                $enable_queuemetrics_logging =	$row['enable_queuemetrics_logging'];
                $queuemetrics_server_ip	=		$row['queuemetrics_server_ip'];
                $queuemetrics_dbname =			$row['queuemetrics_dbname'];
                $queuemetrics_login	=			$row['queuemetrics_login'];
                $queuemetrics_pass =			$row['queuemetrics_pass'];
                $queuemetrics_log_id =			$row['queuemetrics_log_id'];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ($enable_queuemetrics_logging > 0) {
                $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
    
                $time_id = 0;
                //$stmt="SELECT time_id,queue,agent from queue_log where call_id='$CallCID' and verb='CONNECT' order by time_id desc limit 1;";
                $linkB->where('call_id', $CallCID);
                $linkB->where('verb', 'CONNECT');
                $linkB->orderBy('time_id', 'desc');
                $rslt = $linkB->getOne('queue_log', 'time_id,queue,agent');
                $VAC_eq_ct = $linkB->getRowCount();
                if ($VAC_eq_ct > 0) {
                    $time_id =	$rslt['time_id'];
                    $queue =	$rslt['queue'];
                    $agent =	$rslt['agent'];
                }
                $StarTtimE = date("U");
                if ($time_id > 100000) 
                    {$seconds = ($StarTtimE - $time_id);}
    
                if ($VAC_eq_ct > 0) {
                    //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$CallCID',queue='$queue',agent='Agent/$user',verb='CALLERONHOLD',data1='PARK',serverid='$queuemetrics_log_id';";
                    $insertData = array(
                        'partition' => 'P01',
                        'time_id' => $StarTtimE,
                        'call_id' => $CallCID,
                        'queue' => $queue,
                        'agent' => "Agent/{$user}",
                        'verb' => 'CALLERONHOLD',
                        'data1' => 'PARK',
                        'serverid' => $queuemetrics_log_id
                    );
                    $rslt = $linkB->insert('queue_log', $insertData);
                    $affected_rows = $linkB->getRowCount();
                }
            }
        }
    
        //$stmt="UPDATE vicidial_live_agents SET external_park='' where user='$user';";
        $astDB->where('user', $user);
        $rslt = $astDB->update('vicidial_live_agents', array( 'external_park' => '' ));
    }
    
    if ($ACTION == "RedirectFromPark") {
        if ( (strlen($channel) < 3) or (strlen($queryCID) < 15) or (strlen($exten) < 1) or (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) ) {
            $channel_live=0;
            $message  = "One of these variables is not valid:\n";
            if (strlen($channel) < 3) {$message .= "'channel' must be greater than 2 characters\n";}
            if (strlen($queryCID) < 15) {$message .= "'queryCID' must be greater than 14 characters\n";}
            if (strlen($exten) < 1) {$message .= "'exten' must be set\n";}
            if (strlen($ext_context) < 1) {$message .= "'ext_context' must be set\n";}
            if (strlen($ext_priority) < 1) {$message .= "'ext_priority' must be set\n";}
            $message .= "\nRedirectFromPark Action not sent\n";
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            //$stmt = "DELETE FROM parked_channels where server_ip='$server_ip' and channel='$channel';";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('channel', $channel);
            $rslt = $astDB->delete('parked_channels');
            $ACTION = "Redirect";
    
            $parked_sec = 0;
            //$stmt = "SELECT UNIX_TIMESTAMP(parked_time) FROM park_log where uniqueid='$uniqueid' and server_ip='$server_ip' and extension='$CallCID' and (parked_sec < 1 or grab_time is NULL) order by parked_time desc limit 1;";
            $astDB->where('uniqueid', $uniqueid);
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $CallCID);
            $astDB->where("(parked_sec < 1 or grab_time is NULL)");
            $astDB->orderBy('parked_time', 'desc');
            $rslt = $astDB->getOne('park_log', 'UNIX_TIMESTAMP(parked_time) as parked_time');
            $VAC_pl_ct = $astDB->getRowCount();
            if ($VAC_pl_ct > 0) {
                $parked_sec	= ($StarTtimE - $rslt['parked_time']);
    
                //$stmt = "UPDATE park_log SET status='GRABBED',grab_time='$NOW_TIME',parked_sec='$parked_sec' where uniqueid='$uniqueid' and server_ip='$server_ip' and extension='$CallCID' order by parked_time desc limit 1;";
                $updateData = array(
                    'status' => 'GRABBED',
                    'grab_time' => $NOW_TIME,
                    'parked_sec' => $parked_sec
                );
                $astDB->where('uniqueid', $uniqueid);
                $astDB->where('server_ip', $server_ip);
                $astDB->where('extension', $CallCID);
                $astDB->orderBy('parked_time', 'desc');
                $rslt = $astDB->update('park_log', $updateData, 1);
    
                #############################################
                ##### START QUEUEMETRICS LOGGING LOOKUP #####
                //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
                $rslt = $astDB->get('system_settings', null, 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id');
                $qm_conf_ct = $astDB->getRowCount();
                $i = 0;
                while ($i < $qm_conf_ct) {
                    $row = $rslt[$i];
                    $enable_queuemetrics_logging =	$row['enable_queuemetrics_logging'];
                    $queuemetrics_server_ip	=		$row['queuemetrics_server_ip'];
                    $queuemetrics_dbname =			$row['queuemetrics_dbname'];
                    $queuemetrics_login	=			$row['queuemetrics_login'];
                    $queuemetrics_pass =			$row['queuemetrics_pass'];
                    $queuemetrics_log_id =			$row['queuemetrics_log_id'];
                    $i++;
                }
                ##### END QUEUEMETRICS LOGGING LOOKUP #####
                ###########################################
                if ($enable_queuemetrics_logging > 0) {
                    $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
    
                    $time_id = 0;
                    //$stmt="SELECT time_id,queue,agent from queue_log where call_id='$CallCID' and verb='CONNECT' order by time_id desc limit 1;";
                    $astDB->where('call_id', $CallCID);
                    $astDB->where('verb', 'CONNECT');
                    $astDB->orderBy('time_id', 'desc');
                    $rslt = $linkB->getOne('queue_log', 'time_id,queue,agent');
                    $VAC_eq_ct = $linkB->getRowCount();
                    if ($VAC_eq_ct > 0) {
                        $time_id =	$rslt['time_id'];
                        $queue =	$rslt['queue'];
                        $agent =	$rslt['agent'];
                    }
                    $StarTtimE = date("U");
                    if ($time_id > 100000) 
                        {$seconds = ($StarTtimE - $time_id);}
    
                    if ($VAC_eq_ct > 0) {
                        //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$CallCID',queue='$queue',agent='Agent/$user',verb='CALLEROFFHOLD',data1='$parked_sec',data2='PARK',serverid='$queuemetrics_log_id';";
                        $insertData = array(
                            'partition' => 'P01',
                            'time_id' => $StarTtimE,
                            'call_id' => $CallCID,
                            'queue' => $queue,
                            'agent' => "Agent/{$user}",
                            'verb' => 'CALLEROFFHOLD',
                            'data1' => $parked_sec,
                            'data2' => 'PARK',
                            'serverid' => $queuemetrics_log_id
                        );
                        $rslt = $linkB->insert('queue_log', $insertData);
                        $affected_rows = $linkB->getRowCount();
                    }
                }
            }
        }
    
        //$stmt="UPDATE vicidial_live_agents SET external_park='' where user='$user';";
        $astDB->where('user', $user);
        $rslt = $astDB->update('vicidial_live_agents', array( 'external_park' => '' ));
    }
    
    if ($ACTION == "RedirectToParkIVR") {
        if ( (strlen($channel) < 3) or (strlen($queryCID) < 15) or (strlen($exten) < 1) or (strlen($extenName) < 1) or (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) or (strlen($parkedby) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            if (strlen($channel) < 3) {$message .= "'channel' must be greater than 2 characters\n";}
            if (strlen($queryCID) < 15) {$message .= "'queryCID' must be greater than 14 characters\n";}
            if (strlen($exten) < 1) {$message .= "'exten' must be set\n";}
            if (strlen($ext_context) < 1) {$message .= "'ext_context' must be set\n";}
            if (strlen($extenName) < 1) {$message .= "'extenName' must be set\n";}
            if (strlen($ext_priority) < 1) {$message .= "'ext_priority' must be set\n";}
            if (strlen($parkedby) < 1) {$message .= "'parkedby' must be set\n";}
            $message .= "\nRedirectToPark Action not sent\n";
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            //$stmt = "INSERT INTO parked_channels values('$channel','$server_ip','$CallCID','$extenName','$parkedby','$NOW_TIME');";
            $insertData = array(
                'channel' => $channel,
                'server_ip' => $server_ip,
                'channel_group' => $CallCID,
                'extension' => $extenName,
                'parked_by' => $parkedby,
                'parked_time' => $NOW_TIME
            );
            $rslt = $astDB->insert('parked_channels', $insertData);
            $ACTION = "Redirect";

            //$stmt = "UPDATE vicidial_auto_calls SET extension='PARK_IVR' where callerid='$CallCID' limit 1;";
            $astDB->where('callerid', $CallCID);
            $rslt = $astDB->update('vicidial_auto_calls', array( 'extension' => 'PARK_IVR' ), 1);
    
            //$stmt = "INSERT INTO park_log SET uniqueid='$uniqueid',status='IVRPARKED',channel='$channel',channel_group='$campaign',server_ip='$server_ip',parked_time='$NOW_TIME',parked_sec=0,extension='$CallCID',user='$user',lead_id='$lead_id';";
            $insertData = array(
                'uniqueid' => $uniqueid,
                'status' => 'IVRPARKED',
                'channel' => $channel,
                'channel_group' => $campaign,
                'server_ip' => $server_ip,
                'parked_time' => $NOW_TIME,
                'parked_sec' => 0,
                'extension' => $CallCID,
                'user' => $user,
                'lead_id' => $lead_id
            );
            $rslt = $astDB->insert('park_log', $insertData);
    
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
            $rslt = $astDB->get('system_settings', null, 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id');
            $qm_conf_ct = $astDB->getRowCount();
            $i = 0;
            while ($i < $qm_conf_ct) {
                $row = $rslt[$i];
                $enable_queuemetrics_logging =	$row['enable_queuemetrics_logging'];
                $queuemetrics_server_ip	=		$row['queuemetrics_server_ip'];
                $queuemetrics_dbname =			$row['queuemetrics_dbname'];
                $queuemetrics_login	=			$row['queuemetrics_login'];
                $queuemetrics_pass =			$row['queuemetrics_pass'];
                $queuemetrics_log_id =			$row['queuemetrics_log_id'];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ($enable_queuemetrics_logging > 0) {
                $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
    
                $time_id = 0;
                //$stmt="SELECT time_id,queue,agent from queue_log where call_id='$CallCID' and verb='CONNECT' order by time_id desc limit 1;";
                $linkB->where('call_id', $CallCID);
                $linkB->where('verb', 'CONNECT');
                $linkB->orderBy('time_id', 'desc');
                $rslt = $linkB->getOne('queue_log', 'time_id,queue,agent');
                $VAC_eq_ct = $linkB->getRowCount();
                if ($VAC_eq_ct > 0) {
                    $time_id =	$rslt['time_id'];
                    $queue =	$rslt['queue'];
                    $agent =	$rslt['agent'];
                }
                $StarTtimE = date("U");
                if ($time_id > 100000) 
                    {$seconds = ($StarTtimE - $time_id);}
    
                if ($VAC_eq_ct > 0) {
                    //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$CallCID',queue='$queue',agent='Agent/$user',verb='CALLERONHOLD',data1='IVRPARK',serverid='$queuemetrics_log_id';";
                    $insertData = array(
                        'partition' => 'P01',
                        'time_id' => $StarTtimE,
                        'call_id' => $CallCID,
                        'queue' => $queue,
                        'agent' => "Agent/{$user}",
                        'verb' => 'CALLERONHOLD',
                        'data1' => 'IVRPARK',
                        'serverid' => $queuemetrics_log_id
                    );
                    $rslt = $linkB->insert('queue_log', $insertData);
                    $affected_rows = $linkB->getRowCount();
                }
            }
        }
    
        //$stmt="UPDATE vicidial_live_agents SET external_park='' where user='$user';";
        $astDB->where('user', $user);
        $rslt = $astDB->update('vicidial_live_agents', array( 'external_park' => '' ));
    }
    
    if ($ACTION == "RedirectFromParkIVR") {
        if ( (strlen($channel) < 3) or (strlen($queryCID) < 15) or (strlen($exten) < 1) or (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            if (strlen($channel) < 3) {$message .= "'channel' must be greater than 2 characters\n";}
            if (strlen($queryCID) < 15) {$message .= "'queryCID' must be greater than 14 characters\n";}
            if (strlen($exten) < 1) {$message .= "'exten' must be set\n";}
            if (strlen($ext_context) < 1) {$message .= "'ext_context' must be set\n";}
            if (strlen($ext_priority) < 1) {$message .= "'ext_priority' must be set\n";}
            $message .= "\nRedirectFromPark Action not sent\n";
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            //$stmt = "DELETE FROM parked_channels where server_ip='$server_ip' and channel='$channel';";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('channel', $channel);
            $rslt = $astDB->delete('parked_channels');
            $ACTION = "Redirect";
    
            //$stmt = "UPDATE vicidial_auto_calls SET extension='' where callerid='$CallCID' limit 1;";
            $astDB->where('callerid', $CallCID);
            $rslt = $astDB->update('vicidial_auto_calls', array( 'extension' => '' ), 1);
    
            $parked_sec = 0;
            //$stmt = "SELECT UNIX_TIMESTAMP(parked_time) FROM park_log where uniqueid='$uniqueid' and server_ip='$server_ip' and extension='$CallCID' and (parked_sec < 1 or grab_time is NULL) order by parked_time desc limit 1;";
            $astDB->where('uniqueid', $uniqueid);
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $CallCID);
            $astDB->where('(parked_sec < 1 or grab_time is NULL)');
            $astDB->orderBy('parked_time', 'desc');
            $rslt = $astDB->getOne('park_log', 'UNIX_TIMESTAMP(parked_time) as parked_time');
            $VAC_pl_ct = $astDB->getRowCount();
            if ($VAC_pl_ct > 0) {
                $parked_sec	= ($StarTtimE - $rslt['parked_time']);
    
                //$stmt = "UPDATE park_log SET status='GRABBEDIVR',grab_time='$NOW_TIME',parked_sec='$parked_sec' where uniqueid='$uniqueid' and server_ip='$server_ip' and extension='$CallCID' order by parked_time desc limit 1;";
                $updateData = array(
                    'status' => 'GRABBEDIVR',
                    'grab_time' => $NOW_TIME,
                    'parked_sec' => $parked_sec
                );
                $astDB->where('uniqueid', $uniqueid);
                $astDB->where('server_ip', $server_ip);
                $astDB->where('extension', $CallCID);
                $astDB->orderBy('parked_time', 'desc');
                $rslt = $astDB->update('park_log', $updateData, 1);
    
                #############################################
                ##### START QUEUEMETRICS LOGGING LOOKUP #####
                //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
                $rslt = $astDB->get('system_settings', null, 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id');
                $qm_conf_ct = $astDB->getRowCount();
                $i = 0;
                while ($i < $qm_conf_ct) {
                    $row = $rslt[$i];
                    $enable_queuemetrics_logging =	$row['enable_queuemetrics_logging'];
                    $queuemetrics_server_ip	=		$row['queuemetrics_server_ip'];
                    $queuemetrics_dbname =			$row['queuemetrics_dbname'];
                    $queuemetrics_login	=			$row['queuemetrics_login'];
                    $queuemetrics_pass =			$row['queuemetrics_pass'];
                    $queuemetrics_log_id =			$row['queuemetrics_log_id'];
                    $i++;
                }
                ##### END QUEUEMETRICS LOGGING LOOKUP #####
                ###########################################
                if ($enable_queuemetrics_logging > 0) {
                    $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
    
                    $time_id = 0;
                    //$stmt="SELECT time_id,queue,agent from queue_log where call_id='$CallCID' and verb='CONNECT' order by time_id desc limit 1;";
                    $linkB->where('call_id', $CallCID);
                    $linkB->where('verb', 'CONNECT');
                    $linkB->orderBy('time_id', 'desc');
                    $rslt = $linkB->getOne('queue_log', 'time_id,queue,agent');
                    $VAC_eq_ct = $linkB->getRowCount();
                    if ($VAC_eq_ct > 0) {
                        $time_id =	$rslt['time_id'];
                        $queue =	$rslt['queue'];
                        $agent =	$rslt['agent'];
                    }
                    $StarTtimE = date("U");
                    if ($time_id > 100000) 
                        {$seconds = ($StarTtimE - $time_id);}
    
                    if ($VAC_eq_ct > 0) {
                        //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$CallCID',queue='$queue',agent='Agent/$user',verb='CALLEROFFHOLD',data1='$parked_sec',data2='IVRPARK',serverid='$queuemetrics_log_id';";
                        $insertData = array(
                            'partition' => 'P01',
                            'time_id' => $StarTtimE,
                            'call_id' => $CallCID,
                            'queue' => $queue,
                            'agent' => "Agent/{$user}",
                            'verb' => 'CALLEROFFHOLD',
                            'data1' => $parked_sec,
                            'data2' => 'IVRPARK',
                            'serverid' => $queuemetrics_log_id
                        );
                        $rslt = $linkB->insert('queue_log', $insertData);
                        $affected_rows = $linkB->getRowCount();
                    }
                }
            }
        }
    
        //$stmt="UPDATE vicidial_live_agents SET external_park='' where user='$user';";
        $astDB->where('user', $user);
        $rslt = $astDB->update('vicidial_live_agents', array( 'external_park' => '' ));
    }
    
    if ($ACTION == "RedirectName") {
        if ( (strlen($channel) < 3) || (strlen($queryCID) < 15)  || (strlen($extenName) < 1)  || (strlen($ext_context) < 1)  || (strlen($ext_priority) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            $message .= "Channel must be greater than 2 characters\n";
            $message .= "queryCID must be greater than 14 characters\n";
            $message .= "extenName must be set\n";
            $message .= "ext_context must be set\n";
            $message .= "ext_priority must be set\n";
            $message .= "\nRedirectName Action not sent\n";
            $APIResult = array( "result" => "error", "message" => $message );
        } else {
            //$stmt="SELECT dialplan_number FROM phones where server_ip = '$server_ip' and extension='$extenName';";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $extenName);
            $rslt = $astDB->get('phones', null, 'dialplan_number');
            $name_count = $astDB->getRowCount();
            if ($name_count > 0) {
                $row = $rslt[0];
                $exten = $row['dialplan_number'];
                $ACTION = "Redirect";
            }
        }
    }
    
    if ($ACTION == "RedirectNameVmail") {
        if ( (strlen($channel)<3) or (strlen($queryCID) < 15)  || (strlen($extenName) < 1)  || (strlen($exten) < 1)  || (strlen($ext_context) < 1)  || (strlen($ext_priority) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            $message .= "Channel must be greater than 2 characters\n";
            $message .= "queryCID must be greater than 14 characters\n";
            $message .= "extenName must be set\n";
            $message .= "exten must be set\n";
            $message .= "ext_context must be set\n";
            $message .= "ext_priority must be set\n";
            $message .= "\nRedirectNameVmail Action not sent\n";
            $APIResult = array( "result" => "error", "message" => $message );
        } else {
            //$stmt="SELECT voicemail_id FROM phones where server_ip = '$server_ip' and extension='$extenName';";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $extenName);
            $rslt = $astDB->get('phones', null, 'voicemail_id');
            $name_count = $astDB->getRowCount();
            if ($name_count > 0) {
                $row = $rslt[0];
                $voicemail_id = $row['voicemail_id'];
                $exten = "$exten$voicemail_id";
                $ACTION = "Redirect";
            }
        }
    }
    
    if ($ACTION == "RedirectXtraCXNeW") {
        $DBout = '';
        $row = '';
        $rowx = '';
        $channel_liveX = 1;
        $channel_liveY = 1;
        if ( (strlen($channel) < 3) || (strlen($queryCID) < 15) || (strlen($ext_context) < 1) or (strlen($ext_priority) < 1) || (strlen($session_id) < 3) || ( ( (strlen($extrachannel) < 3) || (strlen($exten) < 1) ) && (!preg_match("/NEXTAVAILABLE/", $exten)) ) ) {
            $channel_liveX = 0;
            $channel_liveY = 0;
            $message  = "One of these variables is not valid:\n";
            $message .= "Channel must be greater than 2 characters\n";
            $message .= "ExtraChannel must be greater than 2 characters\n";
            $message .= "queryCID must be greater than 14 characters\n";
            $message .= "exten must be set\n";
            $message .= "ext_context must be set\n";
            $message .= "ext_priority must be set\n";
            $message .= "\nRedirect Action not sent\n";
            if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
                //if ($WeBRooTWritablE > 0) {
                //    $fp = fopen ("./vicidial_debug.txt", "a");
                //    fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|\n");
                //    fclose($fp);
                //}
            }
            $APIResult = array( "result" => "error", "message" => $message );
        } else {
            $exitThis = 0;
            $message = '';
            $result = 'error';
            if (preg_match("/NEXTAVAILABLE/", $exten)) {
                //$stmtA="SELECT * FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
                $rslt = $astDB->rawQuery("SELECT * FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';");
                $row_ct = $astDB->getRowCount();
                $lastSQL = $astDB->getLastQuery();
                if ($row_ct > 1) {
                    //$stmtB="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
                    $rslt = $astDB->rawQuery("UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;");
    
                    //$stmtC="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('extension', "$protocol/$extension$NOWnum");
                    $astDB->where('conf_exten', $session_id, '!=');
                    $rslt = $astDB->get('vicidial_conferences', null, 'conf_exten');
                    $row = $rslt[0];
                    $exten = $row['conf_exten'];
    
                    if ( (preg_match("/^8300/i", $extension)) && ($protocol == 'Local') ) {
                        $extension = "$extension$user";
                    }
    
                    //$stmtD="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('conf_exten', $exten);
                    $rslt = $astDB->update('vicidial_conferences', array('extension' => "$protocol/$extension"), 1);
    
                    //$stmtE="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('conf_exten', $session_id);
                    $rslt = $astDB->update('vicidial_conferences', array('leave_3way' => 1, 'leave_3way_datetime' => $NOW_TIME, 'extension' => "3WAY_$user"));
    
                    $queryCID = "CXAR24$NOWnum";
                    //$stmtF="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
                    $insertData = array(
                        'man_id' => '',
                        'uniqueid' => '',
                        'entry_date' => $NOW_TIME,
                        'status' => 'NEW',
                        'response' => 'N',
                        'server_ip' => $server_ip,
                        'channel' => '',
                        'action' => 'Redirect',
                        'callerid' => $queryCID,
                        'cmd_line_b' => "Channel: $agentchannel",
                        'cmd_line_c' => "Context: $ext_context",
                        'cmd_line_d' => "Exten: $exten",
                        'cmd_line_e' => 'Priority: 1',
                        'cmd_line_f' => "Callerid: $queryCID",
                        'cmd_line_g' => '',
                        'cmd_line_h' => '',
                        'cmd_line_i' => '',
                        'cmd_line_j' => '',
                        'cmd_line_k' => ''
                    );
                    $rslt = $astDB->insert('vicidial_manager', $insertData);
    
                    //$stmtG="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('user', $user);
                    $rslt = $astDB->update('vicidial_live_agents', array('conf_exten' => $exten));
                    $lastSQL = $astDB->getLastQuery();
    
                    if ($auto_dial_level < 1) {
                        //$stmtH = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
                        $astDB->where('lead_id', $lead_id);
                        $astDB->where('callerid', 'M%', 'like');
                        $rslt = $astDB->delete('vicidial_auto_calls');
                    }
    
                //	$fp = fopen ("./vicidial_debug_3way.txt", "a");
                //	fwrite ($fp, "$NOW_TIME|$filename|\n|$stmtA|\n|$stmtB|\n|$stmtC|\n|$stmtD|\n|$stmtE|\n|$stmtF|\n|$stmtG|\n|$stmtH|\n\n");
                //	fclose($fp);
    
                    //echo "NeWSessioN|$exten|\n";
                    //echo "|$stmtG|\n";
                    
                    $APIResult = array( "result" => "success", "new_session" => $exten, 'sql' => "$lastSQL" );
                    $exitThis = 1;
                } else {
                    $channel_liveX = 0;
                    $message .= "Cannot find empty vicidial_conference on $server_ip, Redirect command not inserted\n|$lastSQL|\n";
                    //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Cannot find empty conference on $server_ip";}
                }
            }
    
            if ($exitThis < 1) {
                if (strlen($call_server_ip) < 7) {$call_server_ip = $server_ip;}
        
                //$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
                $astDB->where('server_ip', $call_server_ip);
                $astDB->where('channel', $channel);
                $rslt = $astDB->get('live_channels');
                $row_ct = $astDB->getRowCount();
                if ($row_ct == 0) {
                    //$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
                    $astDB->where('server_ip', $call_server_ip);
                    $astDB->where('channel', $channel);
                    $rslt = $astDB->get('live_sip_channels');
                    $rowx_ct = $astDB->getRowCount();
                    if ($rowx_ct == 0) {
                        $channel_liveX = 0;
                        $message .= "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                        //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $call_server_ip";}
                    }	
                }
                //$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                $astDB->where('server_ip', $server_ip);
                $astDB->where('channel', $extrachannel);
                $rslt = $astDB->get('live_channels');
                $row_ct = $astDB->getRowCount();
                if ($row_ct == 0) {
                    //$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('channel', $extrachannel);
                    $rslt = $astDB->get('live_sip_channels');
                    $rowx_ct = $astDB->getRowCount();
                    if ($rowx_ct == 0) {
                        $channel_liveY = 0;
                        $message .= "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                        //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $server_ip";}
                    }	
                }
                if ( ($channel_liveX == 1) && ($channel_liveY == 1) ) {
                    //$stmt="SELECT count(*) FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('user', $user, '!=');
                    $rslt = $astDB->get('vicidial_live_agents');
                    $rowx_ct = $astDB->getRowCount();
                    if ($rowx_ct < 1) {
                        $channel_liveY = 0;
                        $message .= "No Local agent to send call to, Redirect command not inserted\n";
                        //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "No Local agent to send call to";}
                    } else {
                        //$stmt="SELECT server_ip,conf_exten,user FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
                        $astDB->where('lead_id', $lead_id);
                        $astDB->where('user', $user, '!=');
                        $rslt = $astDB->get('vicidial_live_agents', null, 'server_ip,conf_exten,user');
                        $rowx = $rslt[0];
                        $dest_server_ip = $rowx['server_ip'];
                        $dest_session_id = $rowx['conf_exten'];
                        $dest_user = $rowx['user'];
                        $S = '*';
        
                        $D_s_ip = explode('.', $dest_server_ip);
                        if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                        if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                        if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                        if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                        if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                        if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                        if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                        if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                        $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S$dest_session_id$S$lead_id$S$dest_user$S$phone_code$S$phone_number$S$campaign$S";
        
                        //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                        $insertData = array(
                            'man_id' => '',
                            'uniqueid' => '',
                            'entry_date' => $NOW_TIME,
                            'status' => 'NEW',
                            'response' => 'N',
                            'server_ip' => $call_server_ip,
                            'channel' => '',
                            'action' => 'Redirect',
                            'callerid' => $queryCID,
                            'cmd_line_b' => "Channel: $channel",
                            'cmd_line_c' => "Context: $ext_context",
                            'cmd_line_d' => "Exten: $dest_dialstring",
                            'cmd_line_e' => "Priority: $ext_priority",
                            'cmd_line_f' => "Callerid: $queryCID",
                            'cmd_line_g' => '',
                            'cmd_line_h' => '',
                            'cmd_line_i' => '',
                            'cmd_line_j' => '',
                            'cmd_line_k' => ''
                        );
                        $rslt = $astDB->insert('vicidial_manager', $insertData);
        
                        //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','$queryCID','Channel: $extrachannel','','','','','','','','','');";
                        $insertData = array(
                            'man_id' => '',
                            'uniqueid' => '',
                            'entry_date' => $NOW_TIME,
                            'status' => 'NEW',
                            'response' => 'N',
                            'server_ip' => $server_ip,
                            'channel' => '',
                            'action' => 'Hangup',
                            'callerid' => $queryCID,
                            'cmd_line_b' => "Channel: $extrachannel",
                            'cmd_line_c' => "",
                            'cmd_line_d' => "",
                            'cmd_line_e' => '',
                            'cmd_line_f' => "",
                            'cmd_line_g' => '',
                            'cmd_line_h' => '',
                            'cmd_line_i' => '',
                            'cmd_line_j' => '',
                            'cmd_line_k' => ''
                        );
                        $rslt = $astDB->insert('vicidial_manager', $insertData);
        
                        $result = 'success';
                        $message .= "RedirectXtraCX command sent for Channel $channel on $call_server_ip and \nHungup $extrachannel on $server_ip\n";
                        //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel on $call_server_ip, Hungup $extrachannel on $server_ip";}
                    }
                } else {
                    if ($channel_liveX == 1) {
                        $ACTION = "Redirect";
                        $server_ip = $call_server_ip;
                    }
                    if ($channel_liveY == 1) {
                        $ACTION = "Redirect";
                        $channel = $extrachannel;
                    }
                    //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Changed to Redirect: $channel on $server_ip";}
                }
        
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
                    if ($WeBRooTWritablE > 0) {
                        //$fp = fopen ("./vicidial_debug.txt", "a");
                        //fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|\n");
                        //fclose($fp);
                    }
                }
            }
        }
    }
    
    if ($ACTION == "RedirectXtraNeW") {
        if ($channel == "$extrachannel") {
            $ACTION = "Redirect";
        } else {
            $row = '';
            $rowx = '';
            $channel_liveX = 1;
            $channel_liveY = 1;
            if ( (strlen($channel) < 3) || (strlen($queryCID) < 15) || (strlen($ext_context) < 1) || (strlen($ext_priority) < 1) || (strlen($session_id) < 3) || ( ( (strlen($extrachannel) < 3) || (strlen($exten) < 1) ) && (!preg_match("/NEXTAVAILABLE/", $exten)) ) ) {
                $channel_liveX = 0;
                $channel_liveY = 0;
                $message  = "One of these variables is not valid:\n";
                $message .= "Channel must be greater than 2 characters\n";
                $message .= "ExtraChannel must be greater than 2 characters\n";
                $message .= "queryCID must be greater than 14 characters\n";
                $message .= "exten must be set\n";
                $message .= "ext_context  must be set\n";
                $message .= "ext_priority must be set\n";
                $message .= "session_id must be set\n";
                $message .= "\nRedirect Action not sent\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
                    if ($WeBRooTWritablE > 0) {
                        //$fp = fopen ("./vicidial_debug.txt", "a");
                        //fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|$session_id|\n");
                        //fclose($fp);
                    }
                }
                $APIResult = array( "result" => "error", "message" => $message );
            } else {
                $exitThis = 0;
                $message = '';
                $result = 'error';
                if (preg_match("/NEXTAVAILABLE/", $exten)) {
                    //$stmt = "SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
                    $rslt = $astDB->rawQuery("SELECT * FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';");
                    $row_ct = $astDB->getRowCount();
                    $lastSQL = $astDB->getLastQuery();
                    if ($row_ct > 1) {
                        //$stmt="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
                        $rslt = $astDB->rawQuery("UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;");
    
                        //$stmt="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
                        $astDB->where('server_ip', $server_ip);
                        $astDB->where('extension', "$protocol/$extension$NOWnum");
                        $astDB->where('conf_exten', $session_id, '!=');
                        $rslt = $astDB->get('vicidial_conferences', null, 'conf_exten');
                        $row = $rslt[0];
                        $exten = $row['conf_exten'];
    
                        //$stmt="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
                        $astDB->where('server_ip', $server_ip);
                        $astDB->where('conf_exten', $exten);
                        $rslt = $astDB->update('vicidial_conferences', array('extension' => "$protocol/$extension"), 1);
    
                        //$stmt="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
                        $astDB->where('server_ip', $server_ip);
                        $astDB->where('conf_exten', $session_id);
                        $rslt = $astDB->update('vicidial_conferences', array('leave_3way' => 1, 'leave_3way_datetime' => $NOW_TIME, 'extension' => "3WAY_$user"));
    
                        $queryCID = "CXAR23$NOWnum";
                        //$stmtB="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
                        $insertData = array(
                            'man_id' => '',
                            'uniqueid' => '',
                            'entry_date' => $NOW_TIME,
                            'status' => 'NEW',
                            'response' => 'N',
                            'server_ip' => $server_ip,
                            'channel' => '',
                            'action' => 'Redirect',
                            'callerid' => $queryCID,
                            'cmd_line_b' => "Channel: $agentchannel",
                            'cmd_line_c' => "Context: $ext_context",
                            'cmd_line_d' => "Exten: $exten",
                            'cmd_line_e' => 'Priority: 1',
                            'cmd_line_f' => "CallerID: $queryCID",
                            'cmd_line_g' => '',
                            'cmd_line_h' => '',
                            'cmd_line_i' => '',
                            'cmd_line_j' => '',
                            'cmd_line_k' => ''
                        );
                        $rslt = $astDB->insert('vicidial_manager', $insertData);
                        $lastSQL = $astDB->getLastQuery();
    
                        //$stmt="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
                        $astDB->where('server_ip', $server_ip);
                        $astDB->where('user', $user);
                        $rslt = $astDB->update('vicidial_live_agents', array('conf_exten' => $exten));
    
                        if ($auto_dial_level < 1) {
                            //$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
                            $astDB->where('lead_id', $lead_id);
                            $astDB->where('callerid', 'M%', 'like');
                            $rslt = $astDB->delete('vicidial_auto_calls');
                        }
    
                        $APIResult = array( "result" => "success", "new_session" => $exten, 'sql' => "$lastSQL" );
                        $exitThis = 1;
                    } else {
                        $channel_liveX = 0;
                        $message .= "Cannot find empty vicidial_conference on $server_ip, Redirect command not inserted\n|$lastSQL|";
                        //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Cannot find empty conference on $server_ip";}
                    }
                }
    
                if ($exitThis < 1) {
                    if (strlen($call_server_ip) < 7) {$call_server_ip = $server_ip;}
        
                    //$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
                    $astDB->where('server_ip', $call_server_ip);
                    $astDB->where('channel', $channel);
                    $rslt = $astDB->get('live_channels');
                    $row_ct = $astDB->getRowCount();
                    if ( ($row_ct == 0) && (!preg_match("/SECOND/", $filename)) ) {
                        //$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
                        $astDB->where('server_ip', $call_server_ip);
                        $astDB->where('channel', $channel);
                        $rslt = $astDB->get('live_sip_channels');
                        $rowx_ct = $astDB->getRowCount();
                        if ($rowx_ct == 0) {
                            $channel_liveX = 0;
                            $message .= "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                            //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $call_server_ip";}
                        }
                    }
                    
                    //$stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('channel', $extrachannel);
                    $rslt = $astDB->get('live_channels');
                    $row_ct = $astDB->getRowCount();
                    if ( ($row_ct == 0) && (!preg_match("/SECOND/", $filename)) ) {
                        //$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                        $astDB->where('server_ip', $server_ip);
                        $astDB->where('channel', $extrachannel);
                        $rslt = $astDB->get('live_sip_channels');
                        $rowx_ct = $astDB->getRowCount();
                        if ($rowx_ct == 0) {
                            $channel_liveY = 0;
                            $message .= "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                            //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $server_ip";}
                        }
                    }
                    if ( ($channel_liveX == 1) && ($channel_liveY == 1) ) {
                        if ( ($server_ip == "$call_server_ip") || (strlen($call_server_ip) < 7) ) {
                            //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','ExtraChannel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','');";
                            $insertData = array(
                                'man_id' => '',
                                'uniqueid' => '',
                                'entry_date' => $NOW_TIME,
                                'status' => 'NEW',
                                'response' => 'N',
                                'server_ip' => $server_ip,
                                'channel' => '',
                                'action' => 'Redirect',
                                'callerid' => $queryCID,
                                'cmd_line_b' => "Channel: $channel",
                                'cmd_line_c' => "ExtraChannel: $extrachannel",
                                'cmd_line_d' => "Context: $ext_context",
                                'cmd_line_e' => "Exten: $exten",
                                'cmd_line_f' => "Priority: $ext_priority",
                                'cmd_line_g' => "CallerID: $queryCID",
                                'cmd_line_h' => '',
                                'cmd_line_i' => '',
                                'cmd_line_j' => '',
                                'cmd_line_k' => ''
                            );
                            $rslt = $astDB->insert('vicidial_manager', $insertData);
        
                            $result = 'success';
                            $message = "RedirectXtra command sent for Channel $channel and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                            //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel and $extrachannel to $exten on $server_ip";}
                        } else {
                            $S = '*';
                            $D_s_ip = explode('.', $server_ip);
                            if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                            if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                            if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                            if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                            if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                            if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                            if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                            if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                            $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S$exten";
        
                            //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                            $insertData = array(
                                'man_id' => '',
                                'uniqueid' => '',
                                'entry_date' => $NOW_TIME,
                                'status' => 'NEW',
                                'response' => 'N',
                                'server_ip' => $call_server_ip,
                                'channel' => '',
                                'action' => 'Redirect',
                                'callerid' => $queryCID,
                                'cmd_line_b' => "Channel: $channel",
                                'cmd_line_c' => "Context: $ext_context",
                                'cmd_line_d' => "Exten: $dest_dialstring",
                                'cmd_line_e' => "Priority: $ext_priority",
                                'cmd_line_f' => "CallerID: $queryCID",
                                'cmd_line_g' => "",
                                'cmd_line_h' => '',
                                'cmd_line_i' => '',
                                'cmd_line_j' => '',
                                'cmd_line_k' => ''
                            );
                            $rslt = $astDB->insert('vicidial_manager', $insertData);
        
                            //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                            $insertData = array(
                                'man_id' => '',
                                'uniqueid' => '',
                                'entry_date' => $NOW_TIME,
                                'status' => 'NEW',
                                'response' => 'N',
                                'server_ip' => $server_ip,
                                'channel' => '',
                                'action' => 'Redirect',
                                'callerid' => $queryCID,
                                'cmd_line_b' => "Channel: $extrachannel",
                                'cmd_line_c' => "Context: $ext_context",
                                'cmd_line_d' => "Exten: $exten",
                                'cmd_line_e' => "Priority: $ext_priority",
                                'cmd_line_f' => "CallerID: $queryCID",
                                'cmd_line_g' => "",
                                'cmd_line_h' => '',
                                'cmd_line_i' => '',
                                'cmd_line_j' => '',
                                'cmd_line_k' => ''
                            );
                            $rslt = $astDB->insert('vicidial_manager', $insertData);
        
                            $result = 'success';
                            $message = "RedirectXtra command sent for Channel $channel on $call_server_ip and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                            //if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";}
                        }
                    } else {
                        if ($channel_liveX == 1) {
                            $ACTION = "Redirect";
                            $server_ip = $call_server_ip;
                        }
                        if ($channel_liveY == 1) {
                            $ACTION = "Redirect";
                            $channel = $extrachannel;
                        }
                    }
        
                    if (preg_match("/SECOND|FIRST|DEBUG/", $filename)) {
                        if ($WeBRooTWritablE > 0) {
                            //$fp = fopen ("./vicidial_debug.txt", "a");
                            //fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|\n");
                            //fclose($fp);
                        }
                    }
                }
            }
        }
    }
    
    if ($ACTION == "Redirect") {
        ### for manual dial VICIDIAL calls send the second attempt to transfer the call
        if ($stage == "2NDXfeR") {
            $local_DEF = 'Local/';
            $local_AMP = '@';
            $hangup_channel_prefix = "$local_DEF$session_id$local_AMP$ext_context";
    
            //$stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('channel', "$hangup_channel_prefix%", 'like');
            $rslt = $astDB->get('live_sip_channels');
            $row_ct = $astDB->getRowCount();
            if ($row_ct > 0) {
                //$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
                $astDB->where('server_ip', $server_ip);
                $astDB->where('channel', "$hangup_channel_prefix%", 'like');
                $rsltx = $astDB->get('live_sip_channels', null, 'channel');
                $rowx = $rsltx[0];
                $channel = $rowx['channel'];
                $channel = preg_replace("/1$/i","2", $channel);
                $queryCID = preg_replace("/^./i","Q", $queryCID);
            }
        }
    
        $row = '';
        $rowx = '';
        $channel_live = 1;
        if ( (strlen($channel) < 3) || (strlen($queryCID) < 15) || (strlen($exten) < 1) || (strlen($ext_context) < 1) || (strlen($ext_priority) < 1) ) {
            $channel_live = 0;
            $message  = "One of these variables is not valid:\n";
            $message .= "Channel $channel must be greater than 2 characters\n";
            $message .= "queryCID $queryCID must be greater than 14 characters\n";
            $message .= "exten $exten must be set\n";
            $message .= "ext_context $ext_context must be set\n";
            $message .= "ext_priority $ext_priority must be set\n";
            $message .= "\nRedirect Action not sent\n";
            $APIResult = array( "result" => "error", "message" => $message );
        } else {
            if (strlen($call_server_ip) > 6) {$server_ip = $call_server_ip;}
            
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
                    $result = 'error';
                    $message .= "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                }
            }
            if ($channel_live == 1) {
                //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                $insertData = array(
                    'man_id' => '',
                    'uniqueid' => '',
                    'entry_date' => $NOW_TIME,
                    'status' => 'NEW',
                    'response' => 'N',
                    'server_ip' => $server_ip,
                    'channel' => '',
                    'action' => 'Redirect',
                    'callerid' => $queryCID,
                    'cmd_line_b' => "Channel: $channel",
                    'cmd_line_c' => "Context: $ext_context",
                    'cmd_line_d' => "Exten: $exten",
                    'cmd_line_e' => "Priority: $ext_priority",
                    'cmd_line_f' => "CallerID: $queryCID",
                    'cmd_line_g' => "",
                    'cmd_line_h' => '',
                    'cmd_line_i' => '',
                    'cmd_line_j' => '',
                    'cmd_line_k' => ''
                );
                $rslt = $astDB->insert('vicidial_manager', $insertData);
    
                $result = 'success';
                $message .= "Redirect command sent for Channel $channel on $server_ip\n";
            }
        }
        
        $APIResult = array( "result" => "$result", "message" => $message );
    }
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>