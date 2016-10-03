<?php
####################################################
#### Name: goGetCallbackCount.php               ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$settings = get_settings('system', $astDB);
$user_group = $agent->user_group;

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }

if ($is_logged_in) {
	$campaignCBhoursSQL = '';
	//$stmt = "SELECT callback_hours_block from vicidial_campaigns where campaign_id='$campaign';";
    $astDB->where('campaign_id', $campaign);
    $rslt = $astDB->getOne('vicidial_campaigns', 'callback_hours_block');
    $camp_count = $astDB->getRowCount();

	if ($camp_count > 0) {
		$callback_hours_block = $rslt['callback_hours_block'];
		if ($callback_hours_block > 0) {
			$x_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-$callback_hours_block, date("i"), date("s"), date("m"), date("d"), date("Y")));
			$campaignCBhoursSQL = "AND entry_time < '{$x_hours_ago}'";
        }
    }
	$campaignCBsql = '';
	if ($system->agentonly_callback_campaign_lock > 0) {
        $campaignCBsql = "AND campaign_id='{$campaign}'";
    }
	$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status NOT IN('INACTIVE','DEAD');";
    $rslt = $astDB->rawQuery($stmt);
	$cbcount = $astDB->getRowCount();

	$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status IN('LIVE');";
    $rslt = $astDB->rawQuery($stmt);
	$cbcount_live = $astDB->getRowCount();

	$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status NOT IN('INACTIVE','DEAD') AND callback_time BETWEEN '$NOW_DATE 00:00:00' AND '$NOW_DATE 23:59:59';";
    $rslt = $astDB->rawQuery($stmt);
	$cbcount_today = $astDB->getRowCount();

	//echo "$cbcount|$cbcount_live|$cbcount_today";
    $APIResult = array( "result" => "success", "data" => array( "callback_count" => $cbcount, "callback_live" => $cbcount_live, "callback_today" => $cbcount_today ));
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>