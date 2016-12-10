<?php
####################################################
#### Name: goAutodialResumePause.php            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$campaign_settings = get_settings('campaign', $astDB, $campaign);
$system_settings = get_settings('system', $astDB);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);

if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }

if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goTask'])) { $ACTION = $astDB->escape($_GET['goTask']); }
    else if (isset($_POST['goTask'])) { $ACTION = $astDB->escape($_POST['goTask']); }
if (isset($_GET['goAgentLog'])) { $agent_log = $astDB->escape($_GET['goAgentLog']); }
    else if (isset($_POST['goAgentLog'])) { $agent_log = $astDB->escape($_POST['goAgentLog']); }
if (isset($_GET['goWrapUp'])) { $wrapup = $astDB->escape($_GET['goWrapUp']); }
    else if (isset($_POST['goWrapUp'])) { $wrapup = $astDB->escape($_POST['goWrapUp']); }
if (isset($_GET['goDialMethod'])) { $dial_method = $astDB->escape($_GET['goDialMethod']); }
    else if (isset($_POST['goDialMethod'])) { $dial_method = $astDB->escape($_POST['goDialMethod']); }
if (isset($_GET['goComments'])) { $comments = $astDB->escape($_GET['goComments']); }
    else if (isset($_POST['goComments'])) { $comments = $astDB->escape($_POST['goComments']); }
if (isset($_GET['goSubStatus'])) { $sub_status = $astDB->escape($_GET['goSubStatus']); }
    else if (isset($_POST['goSubStatus'])) { $sub_status = $astDB->escape($_POST['goSubStatus']); }
if (isset($_GET['goQMExtension'])) { $qm_extension = $astDB->escape($_GET['goQMExtension']); }
    else if (isset($_POST['goQMExtension'])) { $qm_extension = $astDB->escape($_POST['goQMExtension']); }
if (isset($_GET['goEnableSipsakMessages'])) { $enable_sipsak_messages = $astDB->escape($_GET['goEnableSipsakMessages']); }
    else if (isset($_POST['goEnableSipsakMessages'])) { $enable_sipsak_messages = $astDB->escape($_POST['goEnableSipsakMessages']); }

if (!isset($comments)) {$comments = '';}
if (!isset($wrapup)) {$wrapup = '';}

if ($is_logged_in) {
    $MT[0]='';
    
    //$vla_autodialSQL='';
    $server_ip = $phone_settings->server_ip;
    $user = $agent->user;
    $user_group = $agent->user_group;
    
    $random = (rand(1000000, 9999999) + 10000000);
    $updateData = array(
        'uniqueid' => 0,
        'callerid' => '',
        'channel' => '',
        'random_id' => $random,
        'comments' => '',
        'last_state_change' => $NOW_TIME
    );
    if (preg_match('/INBOUND_MAN/', $campaign_settings->dial_method)) {
        //$vla_autodialSQL = ",outbound_autodial='N'";
        $updateData = array_merge( $updateData, array( 'outbound_autodial' => 'N' ) );
    }
    //$stmt="UPDATE vicidial_live_agents set uniqueid=0,callerid='',channel='', random_id='$random',comments='',last_state_change='$NOW_TIME' $vla_autodialSQL where user='$user' and server_ip='$server_ip';";
    $astDB->where('user', $user);
    $astDB->where('server_ip', $server_ip);
    $rslt = $astDB->update('vicidial_live_agents', $updateData);
    $errno = $astDB->getLastError();
    $retry_count = 0;
    while ( (strlen($errno) > 0) and ($retry_count < 9) ) {
        $astDB->where('user', $user);
        $astDB->where('server_ip', $server_ip);
        $rslt = $astDB->update('vicidial_live_agents', $updateData);
        $errno = $astDB->getLastError();
        $retry_count++;
    }
    
    if ($comments != 'NO_STATUS_CHANGE') {
        //$vla_lead_wipeSQL='';
        $updateData = array(
            'status' => $stage
        );
        if ($ACTION == 'VDADready') {
            //$vla_lead_wipeSQL = ",lead_id=0";
            $updateData = array_merge( $updateData, array( 'lead_id' => 0 ) );
        }
        if ($ACTION == 'VDADpause') {
            //$vla_ring_resetSQL = ",ring_callerid=''";
            $updateData = array_merge( $updateData, array( 'ring_callerid' => '' ) );
        }
        //$stmt="UPDATE vicidial_live_agents set status='$stage' $vla_lead_wipeSQL $vla_ring_resetSQL where user='$user' and server_ip='$server_ip';";
        $astDB->where('user', $user);
        $astDB->where('server_ip', $server_ip);
        $rslt = $astDB->update('vicidial_live_agents', $updateData);
        $errno = $astDB->getLastError();
        $retry_count = 0;
        while ( (strlen($errno) > 0) and ($retry_count < 9) ) {
            $astDB->where('user', $user);
            $astDB->where('server_ip', $server_ip);
            $rslt = $astDB->update('vicidial_live_agents', $updateData);
            $errno = $astDB->getLastError();
            $retry_count++;
        }
        $affected_rows = $astDB->getRowCount();
    }
    if ( ($affected_rows > 0) || ($comments == 'NO_STATUS_CHANGE') ) {
        #############################################
        ##### START QUEUEMETRICS LOGGING LOOKUP #####
        //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append FROM system_settings;";
        $queuemetrics = get_settings('queuemetrics', $astDB);
        ##### END QUEUEMETRICS LOGGING LOOKUP #####
        ###########################################
        if ($queuemetrics->enable_queuemetrics_logging > 0) {
            if ( (preg_match('/READY/', $stage)) or (preg_match('/CLOSER/', $stage)) ) {$QMstatus = 'UNPAUSEALL';}
            if (preg_match('/PAUSE/', $stage)) {$QMstatus = 'PAUSEALL';}
            $qmDB = new MySQLiDB($queuemetrics->queuemetrics_server_ip, $queuemetrics->queuemetrics_login, $queuemetrics->queuemetrics_pass, $queuemetrics->queuemetrics_dbname);
    
            $data4SQL='';
            //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
            $astDB->where('campaign_id', $campaign);
            $astDB->where('queuemetrics_phone_environment', '', '!=');
            $rslt = $astDB->getOne('vicidial_campaigns', 'queuemetrics_phone_environment');
            $cqpe_ct = $astDB->getRowCount();
            
            $insertData = array(
                'partition' => 'P01',
                'time_id' => $StarTtimE,
                'call_id' => 'NONE',
                'queue' => 'NONE',
                'agent' => "Agent/$user",
                'verb' => $QMstatus,
                'serverid' => $queuemetrics->queuemetrics_log_id
            );
            
            if ($cqpe_ct > 0) {
                $pe_append = '';
                if ( ($queuemetrics->queuemetrics_pe_phone_append > 0) and (strlen($rslt['queuemetrics_phone_environment'])>0) )
                    {$pe_append = "-$qm_extension";}
                $data4SQL = ",data4='$row[0]$pe_append'";
                $insertData = array_merge( $insertData, array( 'data4' => "{$rslt['queuemetrics_phone_environment']}{$pe_append}" ) );
            }
    
            //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$user',verb='$QMstatus',serverid='$queuemetrics_log_id' $data4SQL;";
            $qmDB->insert('queue_log', $insertData);
            $affected_rows = $qmDB->getRowCount();
    
            $qmDB->__destruct();
        }
    }
    
    $pause_sec = 0;
    //$stmt = "SELECT pause_epoch,pause_sec,wait_epoch,wait_sec,dispo_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
    $astDB->where('agent_log_id', $agent_log_id);
    $rslt = $astDB->getOne('vicidial_agent_log', 'pause_epoch,pause_sec,wait_epoch,wait_sec,dispo_epoch');
    $VDpr_ct = $astDB->getRowCount();
    if ($VDpr_ct > 0) {
        $dispo_epoch = $rslt['dispo_epoch'];
        $wait_sec = 0;
        if ($rslt['wait_epoch'] > 0) {
            $wait_sec = (($StarTtimE - $rslt['wait_epoch']) + $rslt['wait_sec']);
        }
        if ( (preg_match("/NULL/i", $rslt['dispo_epoch'])) or ($rslt['dispo_epoch'] < 1000) )
            {$pause_sec = (($StarTtimE - $rslt['pause_epoch']) + $rslt['pause_sec']);}
        else
            {$pause_sec = (($rslt['dispo_epoch'] - $rslt['pause_epoch']) + $rslt['pause_sec']);}
    }
    
    if ($ACTION == 'VDADready') {
        if ( (preg_match("/NULL/i", $dispo_epoch)) or ($dispo_epoch < 1000) ) {
            //$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtimE' where agent_log_id='$agent_log_id';";
            $astDB->where('agent_log_id', $agent_log_id);
            $rslt = $astDB->update('vicidial_agent_log', array( 'pause_sec' => $pause_sec, 'wait_epoch' => $StarTtimE ));
        }
    }
    
    if ($ACTION == 'VDADpause') {
        if ( (preg_match("/NULL/i", $dispo_epoch)) or ($dispo_epoch < 1000) ) {
            //$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec' where agent_log_id='$agent_log_id';";
            $astDB->where('agent_log_id', $agent_log_id);
            $rslt = $astDB->update('vicidial_agent_log', array( 'wait_sec' => $wait_sec ));
        }
        
        $agent_log = 'NEW_ID';
    }
    
    if ($wrapup == 'WRAPUP') {
        if ( (preg_match("/NULL/i",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
            //$stmt="UPDATE vicidial_agent_log set dispo_epoch='$StarTtimE', dispo_sec='0' where agent_log_id='$agent_log_id';";
            $updateData = array(
                'dispo_epoch' => $StarTtimE,
                'dispo_sec' => '0'
            );
        } else {
            $dispo_sec = ($StarTtimE - $dispo_epoch);
            //$stmt="UPDATE vicidial_agent_log set dispo_sec='$dispo_sec' where agent_log_id='$agent_log_id';";
            $updateData = array(
                'dispo_sec' => $dispo_sec
            );
        }
        $astDB->where('agent_log_id', $agent_log_id);
        $rslt = $astDB->update('vicidial_agent_log', $updateData);
    }
    
    if ($agent_log == 'NEW_ID') {
        //$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtimE','0','$StarTtimE','$user_group');";
        $insertData = array(
            'user' => $user,
            'server_ip' => $server_ip,
            'event_time' => $NOW_TIME,
            'campaign_id' => $campaign,
            'pause_epoch' => $StarTtimE,
            'pause_sec' => 0,
            'wait_epoch' => $StarTtimE,
            'user_group' => $user_group
        );
        $rslt = $astDB->insert('vicidial_agent_log', $insertData);
        $affected_rows = $astDB->getRowCount();
        $agent_log_id = $astDB->getInsertId();
    
        //$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';";
        $astDB->where('user', $user);
        $rslt = $astDB->update('vicidial_live_agents', array( 'agent_log_id' => $agent_log_id ));
        $VLAaffected_rows_update = $astDB->getRowCount();
    }
    
    if (strlen($sub_status) > 0) {
        ### if a pause code(sub_status) is sent with this pause request, continue on to that action without printing output
        $stage = 0;
        $status = $sub_status;
    
        if ($stage < 1) {
            //$stmt="UPDATE vicidial_agent_log set sub_status=\"$status\" where agent_log_id >= '$agent_log_id' and user='$user' and ( (sub_status is NULL) or (sub_status='') )order by agent_log_id limit 2;";
            $rslt = $astDB->rawQuery("UPDATE vicidial_agent_log set sub_status='$status' where agent_log_id >= '$agent_log_id' and user='$user' and ( (sub_status is NULL) or (sub_status='') ) order by agent_log_id limit 2;");
            $affected_rows = $astDB->getRowCount();
        }
    
        ### if entry accepted, add a queue_log entry if QM integration is enabled
        if ($affected_rows > 0) {
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
            $queuemetrics = get_settings('queuemetrics', $astDB);
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
            if ( ($enable_sipsak_messages > 0) and ($queuemetrics->allow_sipsak_messages > 0) and (preg_match("/SIP/i", $phone_settings->protocol)) ) {
                $SIPSAK_prefix = 'BK-';
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$status\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            }
            
            if ($queuemetrics->enable_queuemetrics_logging > 0) {
                $pause_call_id = 'NONE';
                if (strlen($campaign_settings->campaign_cid) > 12) {$pause_call_id = $campaign_cid;}
                $qmDB = new MySQLiDB($queuemetrics->queuemetrics_server_ip, $queuemetrics->queuemetrics_login, $queuemetrics->queuemetrics_pass, $queuemetrics->queuemetrics_dbname);
    
                //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$pause_call_id',queue='NONE',agent='Agent/$user',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='$status';";
                $insertData = array(
                    'partition' => 'P01',
                    'time_id' => $StarTtimE,
                    'call_id' => $pause_call_id,
                    'queue' => 'NONE',
                    'agent' => "Agent/$user",
                    'verb' => 'PAUSEREASON',
                    'serverid' => $queuemetrics_log_id,
                    'data1' => $status
                );
                $rslt = $qmDB->insert('queue_log', $insertData);
                $affected_rows = $qmDB->getRowCount();
    
                $qmDB->__destruct();
            }
        }
        //echo ' Pause Code ' . $status . " has been recorded\nNext agent_log_id:\n" . $agent_log_id . "\n";
        $APIResult = array( "result" => "success", "data" => array( 'pause_code' => $status, 'agent_log_id' => $agent_log_id, 'message' => "Pause Code $status has been recorded. Next agent_log_id is $agent_log_id" ) );
    } else {
        //echo 'Agent ' . $user . ' is now in status ' . $stage . "\nNext agent_log_id:\n$agent_log_id\n";
        $APIResult = array( "result" => "success", "data" => array( 'status' => $stage, 'agent_log_id' => $agent_log_id, 'message' => "Agent $user is now in status $stage. Next agent_log_id is $agent_log_id" ) );
    }
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>