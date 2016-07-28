<?php
####################################################
#### Name: goClearAPIField.php                  ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goComments'])) { $comments = $astDB->escape($_GET['goComments']); }
    else if (isset($_POST['goComments'])) { $comments = $astDB->escape($_POST['goComments']); }

$user = $agent->user;

if ($is_logged_in) {
	//$stmt="UPDATE vicidial_live_agents SET $comments='' where user='$user';";
    $astDB->where('user', $user);
    $rslt = $astDB->update('vicidial_live_agents', array( "$comments" => '' ));

	//echo "DONE: $comments";
    $APIResult = array( "result" => "success", "message" => "DONE: $comments" );
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>