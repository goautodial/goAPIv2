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
?>