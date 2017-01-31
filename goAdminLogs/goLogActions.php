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


$insertData = array(
    'user' => $user,
    'ip_address' => $ip_address,
    'event_date' => $NOW_TIME,
    'action' => $action,
    'details' => $details,
    'db_query' => $db_query,
    'user_group' => $user_group
);
$goDB->insert('go_action_logs', $insertData);

$APIResult = array( "result" => "success", "message" => "Action made by a user successfully logged." );
?>