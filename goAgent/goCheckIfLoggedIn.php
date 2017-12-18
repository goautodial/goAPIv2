<?php
####################################################
#### Name: goCheckIfLoggedIn.php                ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goTask'])) { $task = $astDB->escape($_GET['goTask']); }
    else if (isset($_POST['goTask'])) { $task = $astDB->escape($_POST['goTask']); }
if (isset($_GET['goUpdateStatus'])) { $is_online = $astDB->escape($_GET['goUpdateStatus']); }
    else if (isset($_POST['goUpdateStatus'])) { $is_online = $astDB->escape($_POST['goUpdateStatus']); }
if (isset($_GET['goCheckLastCall'])) { $check_last_call = $astDB->escape($_GET['goCheckLastCall']); }
    else if (isset($_POST['goCheckLastCall'])) { $check_last_call = $astDB->escape($_POST['goCheckLastCall']); }

if (!isset($task) || (isset($task) && $task === '')) {
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    $extension = $phone_settings->extension;
    $protocol = $phone_settings->protocol;
    if ($protocol == 'EXTERNAL') {
        $protocol = 'Local';
        $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
    }
    
    $astDB->where('sess_agent_user', $goUser);
    $astDB->where('sess_agent_phone', $phone_login);
    $astDB->where('sess_agent_status', 'INUSE');
    $rslt = $astDB->getOne('go_agent_sessions');
    $go_agent_sessions = $astDB->getRowCount();
    
    $astDB->where('extension', $extension);
    $astDB->where('server_ip', $phone_settings->server_ip);
    $astDB->where('session_name', $session_name);
    $astDB->where('program', 'vicidial');
    $rslt = $astDB->getOne('web_client_sessions');
    $web_client_sessions = $astDB->getRowCount();
    
    $last_call_is_null = 0;
    $added_message = '';
    if ($check_last_call) {
        //$stmt = "SELECT * FROM vicidial_agent_log WHERE user='$goUser' AND (lead_id IS NOT NULL AND lead_id > '0') AND pause_sec >= '65535' AND status IS NULL AND sub_status IS NULL AND event_time >= NOW() - INTERVAL 30 MINUTE ORDER BY event_time DESC LIMIT 1;";
        $stmt = "SELECT * FROM vicidial_agent_log val LEFT JOIN vicidial_log vl ON val.uniqueid=vl.uniqueid AND val.lead_id!=vl.lead_id WHERE val.user='$goUser' AND (val.lead_id IS NOT NULL AND val.lead_id > '0') AND val.pause_sec >= '65535' AND val.status IS NULL AND val.sub_status IS NULL AND val.event_time >= NOW() - INTERVAL 30 MINUTE ORDER BY val.event_time DESC LIMIT 1;";
        $rslt = $astDB->rawQuery($stmt);
        $last_call_is_null = $astDB->getRowCount();
        $added_message = " There was a problem with your session. Reloading the dialer.";
    }
    
    $is_logged_in = 0;
    $message = "You have been logged out from the dialer.{$added_message}";
    if ($go_agent_sessions > 0 && $web_client_sessions > 0 && $last_call_is_null < 1) {
        $is_logged_in = 1;
        $message = "You're currently logged in on the dialer.";
    }

    $APIResult = array( "result" => "success", "logged_in" => $is_logged_in, "last_call_is_null" => $last_call_is_null, "message" => $message );
} else {
    if ($task == 'check') {
        $is_online = 0;
        $goDB->where('name', $goUser);
        $rslt = $goDB->getOne('users', 'online');
        if ($rslt) {
            $is_online = $rslt['online'];
        }
    } else if ($task == 'update') {
        $goDB->where('name', $goUser);
        $rslt = $goDB->update('users', array('online' => $is_online));
    }
    
    $APIResult = array( "result" => "success", "is_online" => (int)$is_online );
}
?>