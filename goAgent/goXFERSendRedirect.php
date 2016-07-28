<?php
####################################################
#### Name: goGetCallsInQueue.php                ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

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
                $stmt = "UPDATE vicidial_closer_log SET end_epoch='$StarTtimE', length_in_sec=(queue_seconds + $seconds), status='XFER' WHERE lead_id='$lead_id' AND call_date > \"$four_hours_ago\" ORDER BY start_epoch DESC LIMIT 1;";
                $rslt = $astDB->rawQuery($stmt);
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
                    $stmt = "UPDATE vicidial_xfer_stats SET xfer_count=(xfer_count+1) WHERE campaign_id='$campaign' AND preset_name='$preset_name';";
                    $rslt = $astDB->rawQuery($stmt);
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
    
    
    if ($ACTION=="RedirectName") {
        if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
            {
            $channel_live=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "extenName $extenName must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "\nRedirectName Action not sent\n";
            }
        else
            {
            $stmt="SELECT dialplan_number FROM phones where server_ip = '$server_ip' and extension='$extenName';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02027',$user,$server_ip,$session_name,$one_mysql_log);}
            $name_count = mysqli_num_rows($rslt);
            if ($name_count>0)
                {
                $row=mysqli_fetch_row($rslt);
                $exten = $row[0];
                $ACTION="Redirect";
                }
            }
        }
    
    if ($ACTION=="RedirectNameVmail")
        {
        if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
            {
            $channel_live=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "extenName $extenName must be set\n";
            echo "exten $exten must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "\nRedirectNameVmail Action not sent\n";
            }
        else
            {
            $stmt="SELECT voicemail_id FROM phones where server_ip = '$server_ip' and extension='$extenName';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02028',$user,$server_ip,$session_name,$one_mysql_log);}
            $name_count = mysqli_num_rows($rslt);
            if ($name_count>0)
                {
                $row=mysqli_fetch_row($rslt);
                $exten = "$exten$row[0]";
                $ACTION="Redirect";
                }
            }
        }
    
    
    
    
    
    
    if ($ACTION=="RedirectXtraCXNeW")
        {
        $DBout='';
        $row='';   $rowx='';
        $channel_liveX=1;
        $channel_liveY=1;
        if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($session_id)<3) or ( ( (strlen($extrachannel)<3) or (strlen($exten)<1) ) and (!preg_match("/NEXTAVAILABLE/",$exten)) ) )
            {
            $channel_liveX=0;
            $channel_liveY=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "ExtraChannel $extrachannel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "exten $exten must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "\nRedirect Action not sent\n";
            if (preg_match("/SECOND|FIRST|DEBUG/",$filename))
                {
                if ($WeBRooTWritablE > 0)
                    {
                    $fp = fopen ("./vicidial_debug.txt", "a");
                    fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|\n");
                    fclose($fp);
                    }
                }
            }
        else
            {
            if (preg_match("/NEXTAVAILABLE/",$exten))
                {
                $stmtA="SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
                    if ($format=='debug') {echo "\n<!-- $stmtA -->";}
                $rslt=mysqli_query($link, $stmtA);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtA,'02029',$user,$server_ip,$session_name,$one_mysql_log);}
                $row=mysqli_fetch_row($rslt);
                if ($row[0] > 1)
                    {
                    $stmtB="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
                        if ($format=='debug') {echo "\n<!-- $stmtB -->";}
                    $rslt=mysqli_query($link, $stmtB);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtB,'02030',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    $stmtC="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
                        if ($format=='debug') {echo "\n<!-- $stmtC -->";}
                    $rslt=mysqli_query($link, $stmtC);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtC,'02031',$user,$server_ip,$session_name,$one_mysql_log);}
                    $row=mysqli_fetch_row($rslt);
                    $exten = $row[0];
    
                    if ( (preg_match("/^8300/i",$extension)) and ($protocol == 'Local') )
                        {
                        $extension = "$extension$user";
                        }
    
                    $stmtD="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
                        if ($format=='debug') {echo "\n<!-- $stmtD -->";}
                    $rslt=mysqli_query($link, $stmtD);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtD,'02032',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    $stmtE="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
                        if ($format=='debug') {echo "\n<!-- $stmtE -->";}
                    $rslt=mysqli_query($link, $stmtE);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtE,'02033',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    $queryCID = "CXAR24$NOWnum";
                    $stmtF="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
                        if ($format=='debug') {echo "\n<!-- $stmtF -->";}
                    $rslt=mysqli_query($link, $stmtF);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtF,'02034',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    $stmtG="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
                        if ($format=='debug') {echo "\n<!-- $stmtG -->";}
                    $rslt=mysqli_query($link, $stmtG);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtG,'02035',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    if ($auto_dial_level < 1)
                        {
                        $stmtH = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
                            if ($format=='debug') {echo "\n<!-- $stmtH -->";}
                        $rslt=mysqli_query($link, $stmtH);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmtH,'02036',$user,$server_ip,$session_name,$one_mysql_log);}
                        }
    
                //	$fp = fopen ("./vicidial_debug_3way.txt", "a");
                //	fwrite ($fp, "$NOW_TIME|$filename|\n|$stmtA|\n|$stmtB|\n|$stmtC|\n|$stmtD|\n|$stmtE|\n|$stmtF|\n|$stmtG|\n|$stmtH|\n\n");
                //	fclose($fp);
    
                    echo "NeWSessioN|$exten|\n";
                    echo "|$stmtG|\n";
                    
                    exit;
                    }
                else
                    {
                    $channel_liveX=0;
                    echo "Cannot find empty vicidial_conference on $server_ip, Redirect command not inserted\n|$stmt|";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Cannot find empty conference on $server_ip";}
                    }
                }
    
            if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
    
            $stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02037',$user,$server_ip,$session_name,$one_mysql_log);}
            $row=mysqli_fetch_row($rslt);
            if ($row[0]==0)
                {
                $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02038',$user,$server_ip,$session_name,$one_mysql_log);}
                $rowx=mysqli_fetch_row($rslt);
                if ($rowx[0]==0)
                    {
                    $channel_liveX=0;
                    echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $call_server_ip";}
                    }	
                }
            $stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02039',$user,$server_ip,$session_name,$one_mysql_log);}
            $row=mysqli_fetch_row($rslt);
            if ($row[0]==0)
                {
                $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02040',$user,$server_ip,$session_name,$one_mysql_log);}
                $rowx=mysqli_fetch_row($rslt);
                if ($rowx[0]==0)
                    {
                    $channel_liveY=0;
                    echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $server_ip";}
                    }	
                }
            if ( ($channel_liveX==1) and ($channel_liveY==1) )
                {
                $stmt="SELECT count(*) FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02041',$user,$server_ip,$session_name,$one_mysql_log);}
                $rowx=mysqli_fetch_row($rslt);
                if ($rowx[0] < 1)
                    {
                    $channel_liveY=0;
                    echo "No Local agent to send call to, Redirect command not inserted\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "No Local agent to send call to";}
                    }	
                else
                    {
                    $stmt="SELECT server_ip,conf_exten,user FROM vicidial_live_agents where lead_id='$lead_id' and user!='$user';";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02042',$user,$server_ip,$session_name,$one_mysql_log);}
                    $rowx=mysqli_fetch_row($rslt);
                    $dest_server_ip = $rowx[0];
                    $dest_session_id = $rowx[1];
                    $dest_user = $rowx[2];
                    $S='*';
    
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
    
                    $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02043',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','$queryCID','Channel: $extrachannel','','','','','','','','','');";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02044',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    echo "RedirectXtraCX command sent for Channel $channel on $call_server_ip and \nHungup $extrachannel on $server_ip\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel on $call_server_ip, Hungup $extrachannel on $server_ip";}
                    }
                }
            else
                {
                if ($channel_liveX==1)
                {$ACTION="Redirect";   $server_ip = $call_server_ip;}
                if ($channel_liveY==1)
                {$ACTION="Redirect";   $channel=$extrachannel;}
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Changed to Redirect: $channel on $server_ip";}
                }
    
            if (preg_match("/SECOND|FIRST|DEBUG/",$filename))
                {
                if ($WeBRooTWritablE > 0)
                    {
                    $fp = fopen ("./vicidial_debug.txt", "a");
                    fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|\n");
                    fclose($fp);
                    }
                }
            }
        }
    
    
    
    
    
    
    
    
    
    
    if ($ACTION=="RedirectXtraNeW")
        {
        if ($channel=="$extrachannel")
        {$ACTION="Redirect";}
        else
            {
            $row='';   $rowx='';
            $channel_liveX=1;
            $channel_liveY=1;
            if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($session_id)<3) or ( ( (strlen($extrachannel)<3) or (strlen($exten)<1) ) and (!preg_match("/NEXTAVAILABLE/",$exten)) ) )
                {
                $channel_liveX=0;
                $channel_liveY=0;
                echo "One of these variables is not valid:\n";
                echo "Channel $channel must be greater than 2 characters\n";
                echo "ExtraChannel $extrachannel must be greater than 2 characters\n";
                echo "queryCID $queryCID must be greater than 14 characters\n";
                echo "exten $exten must be set\n";
                echo "ext_context $ext_context must be set\n";
                echo "ext_priority $ext_priority must be set\n";
                echo "session_id $session_id must be set\n";
                echo "\nRedirect Action not sent\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename))
                    {
                    if ($WeBRooTWritablE > 0)
                        {
                        $fp = fopen ("./vicidial_debug.txt", "a");
                        fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|$session_id|\n");
                        fclose($fp);
                        }
                    }
                }
            else
                {
                if (preg_match("/NEXTAVAILABLE/",$exten))
                    {
                    $stmt="SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id';";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02045',$user,$server_ip,$session_name,$one_mysql_log);}
                    $row=mysqli_fetch_row($rslt);
                    if ($row[0] > 1)
                        {
                        $stmt="UPDATE vicidial_conferences set extension='$protocol/$extension$NOWnum', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) and conf_exten != '$session_id' limit 1;";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02046',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        $stmt="SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and extension='$protocol/$extension$NOWnum' and conf_exten != '$session_id';";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02047',$user,$server_ip,$session_name,$one_mysql_log);}
                        $row=mysqli_fetch_row($rslt);
                        $exten = $row[0];
    
                        $stmt="UPDATE vicidial_conferences set extension='$protocol/$extension' where server_ip='$server_ip' and conf_exten='$exten' limit 1;";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02048',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        $stmt="UPDATE vicidial_conferences set leave_3way='1', leave_3way_datetime='$NOW_TIME', extension='3WAY_$user' where server_ip='$server_ip' and conf_exten='$session_id';";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02049',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        $queryCID = "CXAR23$NOWnum";
                        $stmtB="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','','','','','');";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmtB);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02050',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        $stmt="UPDATE vicidial_live_agents set conf_exten='$exten' where server_ip='$server_ip' and user='$user';";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02051',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        if ($auto_dial_level < 1)
                            {
                            $stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and callerid LIKE \"M%\";";
                                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                            $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02052',$user,$server_ip,$session_name,$one_mysql_log);}
                            }
    
                        echo "NeWSessioN|$exten|\n";
                        echo "|$stmtB|\n";
                        
                        exit;
                        }
                    else
                        {
                        $channel_liveX=0;
                        echo "Cannot find empty vicidial_conference on $server_ip, Redirect command not inserted\n|$stmt|";
                        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "Cannot find empty conference on $server_ip";}
                        }
                    }
    
                if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
    
                $stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02053',$user,$server_ip,$session_name,$one_mysql_log);}
                $row=mysqli_fetch_row($rslt);
                if ( ($row[0]==0) and (!preg_match("/SECOND/",$filename)) )
                    {
                    $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$call_server_ip' and channel='$channel';";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02054',$user,$server_ip,$session_name,$one_mysql_log);}
                    $rowx=mysqli_fetch_row($rslt);
                    if ($rowx[0]==0)
                        {
                        $channel_liveX=0;
                        echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $call_server_ip";}
                        }	
                    }
                $stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02055',$user,$server_ip,$session_name,$one_mysql_log);}
                $row=mysqli_fetch_row($rslt);
                if ( ($row[0]==0) and (!preg_match("/SECOND/",$filename)) )
                    {
                    $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$extrachannel';";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02056',$user,$server_ip,$session_name,$one_mysql_log);}
                    $rowx=mysqli_fetch_row($rslt);
                    if ($rowx[0]==0)
                        {
                        $channel_liveY=0;
                        echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel is not live on $server_ip";}
                        }	
                    }
                if ( ($channel_liveX==1) and ($channel_liveY==1) )
                    {
                    if ( ($server_ip=="$call_server_ip") or (strlen($call_server_ip)<7) )
                        {
                        $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','ExtraChannel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','');";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02057',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        echo "RedirectXtra command sent for Channel $channel and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel and $extrachannel to $exten on $server_ip";}
                        }
                    else
                        {
                        $S='*';
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
    
                        $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02058',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                            if ($format=='debug') {echo "\n<!-- $stmt -->";}
                        $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02059',$user,$server_ip,$session_name,$one_mysql_log);}
    
                        echo "RedirectXtra command sent for Channel $channel on $call_server_ip and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {$DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";}
                        }
                    }
                else
                    {
                    if ($channel_liveX==1)
                    {$ACTION="Redirect";   $server_ip = $call_server_ip;}
                    if ($channel_liveY==1)
                    {$ACTION="Redirect";   $channel=$extrachannel;}
                    }
    
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename))
                    {
                    if ($WeBRooTWritablE > 0)
                        {
                        $fp = fopen ("./vicidial_debug.txt", "a");
                        fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|\n");
                        fclose($fp);
                        }
                    }
                }
            }
        }
    
    
    
    
    
    if ($ACTION=="Redirect")
        {
        ### for manual dial VICIDIAL calls send the second attempt to transfer the call
        if ($stage=="2NDXfeR")
            {
            $local_DEF = 'Local/';
            $local_AMP = '@';
            $hangup_channel_prefix = "$local_DEF$session_id$local_AMP$ext_context";
    
            $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02060',$user,$server_ip,$session_name,$one_mysql_log);}
            $row=mysqli_fetch_row($rslt);
            if ($row > 0)
                {
                $stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$hangup_channel_prefix%\";";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02061',$user,$server_ip,$session_name,$one_mysql_log);}
                $rowx=mysqli_fetch_row($rslt);
                $channel=$rowx[0];
                $channel = preg_replace("/1$/i","2",$channel);
                $queryCID = preg_replace("/^./i","Q",$queryCID);
                }
            }
    
        $row='';   $rowx='';
        $channel_live=1;
        if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) )
            {
            $channel_live=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "exten $exten must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "\nRedirect Action not sent\n";
            }
        else
            {
            if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
            $stmt="SELECT count(*) FROM live_channels where server_ip = '$server_ip' and channel='$channel';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02062',$user,$server_ip,$session_name,$one_mysql_log);}
            $row=mysqli_fetch_row($rslt);
            if ($row[0]==0)
                {
                $stmt="SELECT count(*) FROM live_sip_channels where server_ip = '$server_ip' and channel='$channel';";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02063',$user,$server_ip,$session_name,$one_mysql_log);}
                $rowx=mysqli_fetch_row($rslt);
                if ($rowx[0]==0)
                    {
                    $channel_live=0;
                    echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                    }	
                }
            if ($channel_live==1)
                {
                $stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','','','','','');";
                    if ($format=='debug') {echo "\n<!-- $stmt -->";}
                $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'02064',$user,$server_ip,$session_name,$one_mysql_log);}
    
                echo "Redirect command sent for Channel $channel on $server_ip\n";
                }
            }
        }
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>