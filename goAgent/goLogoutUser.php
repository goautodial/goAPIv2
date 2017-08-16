<?php
####################################################
#### Name: goLogoutPhone.php                    ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$agent = get_settings('user', $astDB, $goUser);

$user = $agent->user;
$user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

if (isset($_GET['goNoDeleteSession'])) { $no_delete_sessions = $astDB->escape($_GET['goNoDeleteSession']); }
    else if (isset($_POST['goNoDeleteSession'])) { $no_delete_sessions = $astDB->escape($_POST['goNoDeleteSession']); }
if (isset($_GET['goLogoutKickAll'])) { $LogoutKickAll = $astDB->escape($_GET['goLogoutKickAll']); }
    else if (isset($_POST['goLogoutKickAll'])) { $LogoutKickAll = $astDB->escape($_POST['goLogoutKickAll']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goUseWebRTC'])) { $use_webrtc = $astDB->escape($_GET['goUseWebRTC']); }
    else if (isset($_POST['goUseWebRTC'])) { $use_webrtc = $astDB->escape($_POST['goUseWebRTC']); }

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($kamDB, $phone_login, $SIPserver, $use_webrtc);

if ($sipIsLoggedIn) {
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    
    $astDB->where('server_ip', $phone_settings->server_ip);
    $query = $astDB->getOne('servers', 'asterisk_version');
    $asterisk_version = $query['asterisk_version'];
    
    $extension = $phone_settings->extension;
    $protocol = $phone_settings->protocol;
    if ($protocol == 'EXTERNAL') {
        $protocol = 'Local';
        $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
    }
    
    if (preg_match("/Zap/i", $protocol)) {
        if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version)) {
            $do_nothing = 1;
        } else {
            $protocol = 'DAHDI';
        }
    }
    
    $server_ip = (strlen($server_ip) > 0) ? $server_ip : $phone_settings->server_ip;
    
    ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
    $SIP_user = "{$protocol}/{$extension}";
    if ( (preg_match('/8300/', $phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
        $SIP_user = "{$protocol}/{$extension}{$phone_login}";
    }
    
    $astDB->where('extension', $SIP_user);
    $astDB->where('server_ip', $server_ip);
    $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
    $prev_login_ct = $astDB->getRowCount();
    
    $i=0;
    while ($i < $prev_login_ct) {
        $conf_exten = $query['conf_exten'];
        $i++;
    }
    
    if (strlen($conf_exten) > 0) {
        $astDB->where('server_ip', $server_ip);
        $astDB->where('user', $user);
        $query = $astDB->getOne('vicidial_live_agents', 'campaign_id');
        $campaign = $query['campaign_id'];
        
		if ($no_delete_sessions < 1) {
			##### Remove the reservation on the vicidial_conferences meetme room
			//$stmt="UPDATE vicidial_conferences set extension='' where server_ip='$server_ip' and conf_exten='$conf_exten';";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('conf_exten', $conf_exten);
            $rslt = $astDB->update('vicidial_conferences', array( 'extension' => '' ));
			$vc_remove = $astDB->getRowCount();
        }

		##### Delete the web_client_sessions and go_agent_sessions
		//$stmt="DELETE from web_client_sessions where server_ip='$server_ip' and session_name ='$session_name';";
        $astDB->where('server_ip', $server_ip);
        $astDB->where('session_name', $session_name);
        $rslt = $astDB->delete('web_client_sessions');
		$wcs_delete = $astDB->getRowCount();
		
		$astDB->where('sess_agent_user', $user);
		$rslt = $astDB->delete('go_agent_sessions');
		$gas_delete = $astDB->getRowCount();
        
        ##### Hangup the client phone
        $astDB->where('server_ip', $server_ip);
        $astDB->where('channel', "$protocol/$extension%", 'like');
        $astDB->orderBy('channel');
        $query = $astDB->getOne('live_sip_channels', 'channel');
        //$query = $db->query("SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;");
        $agent_channel = '';
        if ($astDB->getRowCount() > 0) {
            $agent_channel = $query['channel'];
            $insertData = array(
                'man_id' => '',
                'uniqueid' => '',
                'entry_date' => $NOW_TIME,
                'status' => 'NEW',
                'response' => 'N',
                'server_ip' => $server_ip,
                'channel' => '',
                'action' => 'Hangup',
                'callerid' => "ULGH3459$StarTtimE",
                'cmd_line_b' => "Channel: $agent_channel",
                'cmd_line_c' => '',
                'cmd_line_d' => '',
                'cmd_line_e' => '',
                'cmd_line_f' => '',
                'cmd_line_g' => '',
                'cmd_line_h' => '',
                'cmd_line_i' => '',
                'cmd_line_j' => '',
                'cmd_line_k' => ''
            );
            $rslt = $astDB->insert('vicidial_manager', $insertData);
        }
        
		if ($LogoutKickAll > 0) {
			$local_DEF = 'Local/5555';
			$local_AMP = '@';
			$kick_local_channel = "{$local_DEF}{$conf_exten}{$local_AMP}{$ext_context}";
			$queryCID = "ULGH3458$StarTtimE";

			//$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$exten');";
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
                'cmd_line_b' => "Channel: $kick_local_channel",
                'cmd_line_c' => "Context: $ext_context",
                'cmd_line_d' => "Exten: 8300",
                'cmd_line_e' => 'Priority: 1',
                'cmd_line_f' => "Callerid: $queryCID",
                'cmd_line_g' => '',
                'cmd_line_h' => '',
                'cmd_line_i' => '',
                'cmd_line_j' => $agent_channel,
                'cmd_line_k' => $conf_exten
            );
            $rslt = $astDB->insert('vicidial_manager', $insertData);
		}
        
        sleep(1);
        
        $astDB->where('server_ip', $server_ip);
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_agents');
        $errmsg = $astDB->getLastError();
        $retry_count = 0;
		while ( (strlen($errmsg) > 0) and ($retry_count < 9) ) {
            $astDB->where('server_ip', $server_ip);
            $astDB->where('user', $user);
            $query = $astDB->delete('vicidial_live_agents');
            $errmsg = $astDB->getLastError();
			$retry_count++;
		}
		$vla_delete = $astDB->getRowCount();
        
		#### agent session ######
        //$stmtagent = "DELETE FROM `go_agent_sessions` WHERE `sess_agent_user` = '$user'";
        $astDB->where('sess_agent_user', $user);
        $rslt = $astDB->delete('go_agent_sessions');
        
        ##### Delete the vicidial_live_inbound_agents records for this session
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_inbound_agents');
        $vlia_delete = $astDB->getRowCount();
        
		$pause_sec = 0;
		//$stmt = "SELECT pause_epoch,pause_sec,wait_epoch,talk_epoch,dispo_epoch,agent_log_id from vicidial_agent_log where agent_log_id >= '$agent_log_id' and user='$user' order by agent_log_id desc limit 1;";
        $astDB->where('agent_log_id', $agent_log_id, '>=');
        $astDB->where('user', $user);
        $astDB->orderBy('agent_log_id', 'desc');
        $rslt = $astDB->getOne('vicidial_agent_log', 'pause_epoch,pause_sec,wait_epoch,talk_epoch,dispo_epoch,agent_log_id');
		$VDpr_ct = $astDB->getRowCount();
		if ( ($VDpr_ct > 0) and (strlen($rslt['talk_epoch'] < 5)) and (strlen($rslt['dispo_epoch'] < 5)) ) {
			$agent_log_id = $rslt['agent_log_id'];
			$pause_sec = (($StarTtimE - $rslt['pause_epoch']) + $rslt['pause_sec']);

			//$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtimE' where agent_log_id='$agent_log_id';";
            $updateData = array(
                'pause_sec' => $pause_sec,
                'wait_epoch' => $StarTtimE
            );
			$astDB->where('agent_log_id', $agent_log_id);
            $rslt = $astDB->update('vicidial_agent_log', $updateData);
        }

        ##### insert an entry on vicidial_user_log
        $NOW_USERLOG = date("Y-m-d H:i:s");
        $NOWepoch_USERLOG = date("U");
        //$stmt = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtimE','$user_group')";
        $insertData = array(
            'user' => $user,
            'event' => 'LOGOUT',
            'campaign_id' => $campaign,
            'event_date' => $NOW_USERLOG,
            'event_epoch' => $NOWepoch_USERLOG,
            'user_group' => $user_group
        );
        $query = $astDB->insert('vicidial_user_log', $insertData);

        $result = 'success';
        $message = "User {$user} has been logged out";
    } else {
        $result = 'error';
        $message = "User {$user} is not logged in";
    }
    
    $APIResult = array( "result" => $result, "message" => $message );
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>