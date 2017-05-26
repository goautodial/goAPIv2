<?php
####################################################
#### Name: goManualDialSkip.php                 ####
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
$sipIsLoggedIn = check_sip_login($kamDB, $phone_login, $SIPserver);

if ($sipIsLoggedIn) {
    if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
        else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
    if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
        else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
    if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
        else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
    if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
        else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
    if (isset($_GET['goCalledCount'])) { $called_count = $astDB->escape($_GET['goCalledCount']); }
        else if (isset($_POST['goCalledCount'])) { $called_count = $astDB->escape($_POST['goCalledCount']); }
    if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
        else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }


	$channel_live = 1;
	if ( (strlen($stage) < 1) || (strlen($called_count) < 1) || (strlen($lead_id) < 1) ) {
		$channel_live = 0;
		$APIResult = array( "result" => "error", "message" => "LEAD NOT REVERTED" );
	} else {
		$called_count = ($called_count - 1);
		### set the lead back to previous status and called_count
		//$stmt = "UPDATE vicidial_list set status='$stage', called_count='$called_count',user='$user' where lead_id='$lead_id';";
		$updateData = array(
			'status' => $status,
			'called_count' => $called_count,
			'user' => $user
		);
		$astDB->where('lead_id', $lead_id);
		$rslt = $astDB->update('vicidial_list', $updateData);

		### log the skip event
		//$stmt = "INSERT INTO vicidial_agent_skip_log set campaign_id='$campaign', previous_status='$stage', previous_called_count='$called_count',user='$user', lead_id='$lead_id', event_date=NOW();";
		$insertData = array(
			'campaign_id' => $campaign,
			'previous_status' => $stage,
			'previous_called_count' => $called_count,
			'user' => $user,
			'lead_id' => $lead_id,
			'event_date' => $NOW_DATE
		);
		$astDB->insert('vicidial_agent_skip_log', $insertData);

		$APIResult = array( "result" => "success", "message" => "LEAD REVERTED" );
	}

} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
