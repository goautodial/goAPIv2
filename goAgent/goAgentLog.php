<?php
/*
#######################################################
#### Name: goAgentLog.php	                       ####
#### Description: API to log agent activities      ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
#### Written by: Chris Lomuntad                    ####
#### License: AGPLv2                               ####
#######################################################
*/

if (isset($_GET['goEvent'])) { $event = $astDB->escape($_GET['goEvent']); }
    else if (isset($_POST['goEvent'])) { $event = $astDB->escape($_POST['goEvent']); }
if (isset($_GET['goUserGroup'])) { $user_group = $astDB->escape($_GET['goUserGroup']); }
    else if (isset($_POST['goUserGroup'])) { $user_group = $astDB->escape($_POST['goUserGroup']); }
if (isset($_GET['goSessionID'])) { $session_id = $astDB->escape($_GET['goSessionID']); }
    else if (isset($_POST['goSessionID'])) { $session_id = $astDB->escape($_POST['goSessionID']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goComputerIP'])) { $computer_ip = $astDB->escape($_GET['goComputerIP']); }
    else if (isset($_POST['goComputerIP'])) { $computer_ip = $astDB->escape($_POST['goComputerIP']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }

$NOW = date("Y-m-d H:i:s");
$NOWepoch = date("U");

$insertData = array(
	"user" => $goUser,
	"event" => strtoupper($event),
	"campaign_id" => $campaign,
	"event_date" => $NOW,
	"event_epoch" => $NOWepoch,
	"user_group" => $user_group,
	"session_id" => $session_id,
	"server_ip" => $server_ip,
	"extension" => $extension,
	"computer_ip" => $computer_ip
);
$astDB->insert('vicidial_user_log', $insertData);

$APIResult = array("result" => "success");
?>