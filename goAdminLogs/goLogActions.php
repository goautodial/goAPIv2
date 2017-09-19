<?php
####################################################
#### Name: goLogActions.php                     ####
#### Type: API for Logging Actions              ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['action'])) { $action = $astDB->escape($_GET['action']); }
    else if (isset($_POST['action'])) { $action = $astDB->escape($_POST['action']); }
if (isset($_GET['user'])) { $user = $astDB->escape($_GET['user']); }
    else if (isset($_POST['user'])) { $user = $astDB->escape($_POST['user']); }
if (isset($_GET['ip_address'])) { $ip_address = $astDB->escape($_GET['ip_address']); }
    else if (isset($_POST['ip_address'])) { $ip_address = $astDB->escape($_POST['ip_address']); }
if (isset($_GET['details'])) { $details = $astDB->escape($_GET['details']); }
    else if (isset($_POST['details'])) { $details = $astDB->escape($_POST['details']); }
if (isset($_GET['user_group'])) { $user_group = $astDB->escape($_GET['user_group']); }
    else if (isset($_POST['user_group'])) { $user_group = $astDB->escape($_POST['user_group']); }
if (isset($_GET['db_query'])) { $db_query = $astDB->escape($_GET['db_query']); }
    else if (isset($_POST['db_query'])) { $db_query = $astDB->escape($_POST['db_query']); }

if ($user === 'sess_expired') {
    $goDB->where('ip_address', $ip_address);
    $goDB->where('action', 'LOGIN');
    $goDB->orderBy('event_date', 'desc');
    $rslt = $goDB->getOne('go_action_logs', 'user, user_group');
    
    $user = $rslt['user'];
    $user_group = $rslt['user_group'];
    $details = "Session expired on user $user";
    
    if ($user_group == 'AGENTS') {
        $NOW = date("Y-m-d H:i:s");
        $NOWepoch = date("U");
        
        $astDB->where('user' => $user);
        $astDB->where('event' => 'LOGIN');
        $astDB->orderBy('event_date', 'desc');
        $sessRslt = $astDB->getOne('vicidial_user_log', 'campaign_id');
        $campaign = $sessRslt['campaign_id'];
        
        $insertData = array(
            "user" => $user,
            "event" => 'AUTO-LOGOUT',
            "campaign_id" => $campaign,
            "event_date" => $NOW,
            "event_epoch" => $NOWepoch,
            "user_group" => $user_group,
            "session_id" => '',
            "server_ip" => '',
            "extension" => '',
            "computer_ip" => ''
        );
        $astDB->insert('vicidial_user_log', $insertData);
    }
}

$insertData = array(
    'user' => $user,
    'ip_address' => $ip_address,
    'event_date' => $NOW_TIME,
    'action' => $action,
    'details' => "$details",
    'db_query' => "$db_query",
    'user_group' => $user_group
);
$goDB->insert('go_action_logs', $insertData);

$APIResult = array( "result" => "success", "message" => "Action made by a user successfully logged." );
?>
