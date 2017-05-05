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

if (!isset($task) && $task === '') {
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
    
    $is_logged_in = 0;
    $message = "You have been logged out from the dialer.";
    if ($go_agent_sessions > 0 && $web_client_sessions > 0) {
        $is_logged_in = 1;
        $message = "You're currently logged in on the dialer.";
    }

    $APIResult = array( "result" => "success", "logged_in" => $is_logged_in, "message" => $message );
} else {
    if ($task == 'check') {
        $is_online = 0;
        $goDB->where('name', $goUser);
        $rslt = $goDB->getOne('users', 'online');
        $is_online = $rslt;
    } else if ($task == 'update') {
        $goDB->where('name', $goUser);
        $rslt = $goDB->update('users', array('online' => $is_online));
    }
    
    $APIResult = array( "result" => "success", "is_online" => $is_online );
}
?>