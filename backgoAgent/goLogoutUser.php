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

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($phone_login, $SIPserver);

if ($sipIsLoggedIn) {
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    
    $astDB->where('server_ip', $phone_settings->server_ip);
    $query = $astDB->getOne('servers', 'asterisk_version');
    $asterisk_version = $query['asterisk_version'];
    
    $extension = $phone_settings->extension;
    if ($phone_settings->protocol == 'EXTERNAL') {
        $protocol = 'Local';
        $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
    }
    
    if (preg_match("/Zap/i",$phone_settings->protocol)) {
        if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version)) {
            $do_nothing = 1;
        } else {
            $protocol = 'DAHDI';
        }
    }
    
    $server_ip = $phone_settings->server_ip;
    
    $astDB->where('server_ip', $server_ip);
    $astDB->where('channel', "$protocol/$extension%", 'like');
    $astDB->orderBy('channel');
    $query = $astDB->getOne('live_sip_channels', 'channel');
    //$query = $db->query("SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;");
    $agent_channel = '';
    if ($astDB->getRowCount() > 0) {
        $agent_channel = $query['channel'];
        $query = $astDB->rawQuery("INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','ULGH3459$StarTtime','Channel: $agent_channel','','','','','','','','','')");
    }
    
    ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
    $SIP_user = "{$protocol}/{$extension}";
    if ( (preg_match('/8300/', $phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
        $SIP_user = "{$protocol}/{$extension}{$login}";
    }
    
    $astDB->where('extension', $SIP_user);
    $astDB->where('server_ip', $server_ip);
    $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
    $prev_login_ct = $astDB->getRowCount();
    
    $i=0;
    while ($i < $prev_login_ct) {
        $session_id = $query['conf_exten'];
        $i++;
    }
    
    if (strlen($session_id) > 0) {
        $query = $astDB->rawQuery("UPDATE vicidial_conferences SET extension='' WHERE server_ip='{$phone_settings->server_ip}' AND conf_exten='{$session_id}'");
        
        $astDB->where('server_ip', $server_ip);
        $astDB->where('user', $user);
        $query = $astDB->getOne('vicidial_live_agents', 'campaign_id');
        $campaign = $query['campaign_id'];
        
        ##### insert an entry on vicidial_user_log
        $query = $astDB->rawQuery("INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtime','$user_group')");
                
        sleep(1);
        
        $astDB->where('server_ip', $server_ip);
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_agents');
        
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_inbound_agents');
        
        $channel = $agent_channel;
        $local_DEF = 'Local/5555';
        $conf_exten = $session_id;
        $local_AMP = '@';
        $ext_context = 'default';
        $kick_local_channel = "$local_DEF$conf_exten$local_AMP$ext_context";
        $queryCID = "ULGH3458$StarTtime";
        
        $query = $astDB->rawQuery("INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$conf_exten')");
        
        $result = 'success';
        $message = "User {$user} has been logged out";
    } else {
        $result = 'error';
        $message = "User {$user} is not logged in";
    }
    
    $APIResult = array( "result" => $result, "message" => $message );
} else {
    $APIResult = array( "result" => "error", "message" => "SIP exten '{$phone_login}' is NOT connected" );
}
?>