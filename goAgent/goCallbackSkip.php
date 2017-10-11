<?php
####################################################
#### Name: goCallbackSkip.php                   ####
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
    if (isset($_GET['goCallbackID'])) { $callback_id = $astDB->escape($_GET['goCallbackID']); }
        else if (isset($_POST['goCallbackID'])) { $callback_id = $astDB->escape($_POST['goCallbackID']); }
	
	$astDB->where('callback_id', $callback_id);
	$rslt = $astDB->getOne('vicidial_callbacks', 'lead_id');
	$lead_id = $rslt['lead_id'];
	$cbExist = $astDB->getRowCount();


	if ($cbExist < 1) {
		$APIResult = array( "result" => "error", "message" => "Callback ID does NOT exist." );
	} else {
		$updateData = array(
			'status' => 'INACTIVE',
			'user' => '',
			'recipient' => 'ANYONE'
		);
		$astDB->where('callback_id', $callback_id);
		$rslt = $astDB->update('vicidial_callbacks', $updateData);

		$updateData = array(
			'called_since_last_reset' => 'N',
			'status' => 'NEW'
		);
		$astDB->where('lead_id', $lead_id);
		$rslt = $astDB->update('vicidial_list', $updateData);

		$APIResult = array( "result" => "success", "message" => "Callback Lead reverted back to queue as NEW." );
	}
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
