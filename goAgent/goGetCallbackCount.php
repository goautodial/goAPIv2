<?php
 /**
 * @file 		goGetCallbackCount.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//$is_logged_in = check_agent_login($astDB, $goUser);
//
//$agent = get_settings('user', $astDB, $goUser);
$settings = get_settings('system', $astDB);
//$user_group = $agent->user_group;

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goUserID'])) { $user_id = $astDB->escape($_GET['goUserID']); }
    else if (isset($_POST['goUserID'])) { $user_id = $astDB->escape($_POST['goUserID']); }

$user = (strlen($user_id) > 0) ? $user_id : $goUser;

$astDB->where('user', $user);
$astDB->where('campaign_id', $campaign);
$astDB->where('status', array('INACTIVE', 'DEAD'), 'NOT IN');
$astDB->where('DATEDIFF(NOW(), callback_time)', '21', '>');
$astDB->where('recipient', 'USERONLY');
$astDB->update('vicidial_callbacks', array('status' => 'INACTIVE'));

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

// All Callbacks
$campaignCBsql = '';
if ($settings->agentonly_callback_campaign_lock > 0 && strlen($campaign) > 0) {
	$campaignCBsql = "AND campaign_id='{$campaign}'";
}
$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status NOT IN('INACTIVE','DEAD') ORDER BY callback_time ASC;";
$rslt = $astDB->rawQuery($stmt);
$cbcount = $astDB->getRowCount();
$cb_all = array();
if ($cbcount) {
	foreach ($rslt as $x => $row) {
		$astDB->where('lead_id', $row['lead_id']);
		$xrslt = $astDB->getOne('vicidial_list', 'phone_number,first_name,last_name,gmt_offset_now');
		$row['phone_number'] = trim($xrslt['phone_number']);
		$row['cust_name'] = trim("{$xrslt['first_name']} {$xrslt['last_name']}");
		$row['short_callback_time'] = relativeTime($row['callback_time'], 1);
		$row['long_callback_time'] = relativeTime($row['callback_time'], 6);
		$row['short_entry_time'] = relativeTime($row['entry_time'], 1);
		$row['long_entry_time'] = relativeTime($row['entry_time'], 6);
		$row['gmt_offset_now'] = trim($xrslt['gmt_offset_now']);
		
		$astDB->where('campaign_id', $row['campaign_id']);
		$xrslt = $astDB->getOne('vicidial_campaigns', 'campaign_name');
		$row['campaign_name'] = $xrslt['campaign_name'];
		
		$cb_all[$x] = $row;
	}
}

// Live Callbacks
$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status IN('LIVE') ORDER BY callback_time ASC;";
$rslt = $astDB->rawQuery($stmt);
$cbcount_live = $astDB->getRowCount();
$cb_live = array();
if ($cbcount_live) {
	foreach ($rslt as $x => $row) {
		$astDB->where('lead_id', $row['lead_id']);
		$xrslt = $astDB->getOne('vicidial_list', 'phone_number,first_name,last_name,gmt_offset_now');
		$row['phone_number'] = trim($xrslt['phone_number']);
		$row['cust_name'] = trim("{$xrslt['first_name']} {$xrslt['last_name']}");
		$row['short_callback_time'] = relativeTime($row['callback_time'], 1);
		$row['long_callback_time'] = relativeTime($row['callback_time'], 6);
		$row['short_entry_time'] = relativeTime($row['entry_time'], 1);
		$row['long_entry_time'] = relativeTime($row['entry_time'], 6);
		$row['gmt_offset_now'] = trim($xrslt['gmt_offset_now']);
		
		$astDB->where('campaign_id', $row['campaign_id']);
		$xrslt = $astDB->getOne('vicidial_campaigns', 'campaign_name');
		$row['campaign_name'] = $xrslt['campaign_name'];
		
		$cb_live[$x] = $row;
	}
}

// Callbacks Today
$stmt = "SELECT * FROM vicidial_callbacks WHERE recipient='USERONLY' AND user='$user' $campaignCBsql $campaignCBhoursSQL AND status NOT IN('INACTIVE','DEAD') AND callback_time BETWEEN '$NOW_DATE 00:00:00' AND '$NOW_DATE 23:59:59' ORDER BY callback_time ASC;";
$rslt = $astDB->rawQuery($stmt);
$cbcount_today = $astDB->getRowCount();
$cb_today = array();
if ($cbcount_today) {
	foreach ($rslt as $x => $row) {
		$astDB->where('lead_id', $row['lead_id']);
		$xrslt = $astDB->getOne('vicidial_list', 'phone_number,first_name,last_name,gmt_offset_now');
		$row['phone_number'] = trim($xrslt['phone_number']);
		$row['cust_name'] = trim("{$xrslt['first_name']} {$xrslt['last_name']}");
		$row['short_callback_time'] = relativeTime($row['callback_time'], 1);
		$row['long_callback_time'] = relativeTime($row['callback_time'], 6);
		$row['short_entry_time'] = relativeTime($row['entry_time'], 1);
		$row['long_entry_time'] = relativeTime($row['entry_time'], 6);
		$row['gmt_offset_now'] = trim($xrslt['gmt_offset_now']);
		
		$astDB->where('campaign_id', $row['campaign_id']);
		$xrslt = $astDB->getOne('vicidial_campaigns', 'campaign_name');
		$row['campaign_name'] = $xrslt['campaign_name'];
		
		$cb_today[$x] = $row;
	}
}


function relativeTime($mysqltime, $maxdepth = 1) {
    $time = strtotime(str_replace('/','-', $mysqltime));
    $d[0] = array(1,_("second"));
    $d[1] = array(60,_("minute"));
    $d[2] = array(3600,_("hour"));
    $d[3] = array(86400,_("day"));
    $d[4] = array(604800,_("week"));
    $d[5] = array(2592000,_("month"));
    $d[6] = array(31104000,_("year"));

    $w = array();

    $depth = 0;
    $return = "";
    $now = time();
    $diff = ($now-$time);
    $secondsLeft = $diff;

    if ($secondsLeft == 0) return "now";

    for($i=6; $i>-1; $i--) {
		$w[$i] = intval($secondsLeft/$d[$i][0]);
		$secondsLeft -= ($w[$i]*$d[$i][0]);
		if($w[$i] != 0) {
			$return .= abs($w[$i]) . " " . $d[$i][1] . ((abs($w[$i]) > 1) ? 's' : '') ." ";
			$depth  += 1;
			if ($depth >= $maxdepth) break;
		}
    }

    $verb = ($diff > 0) ? "" : "in ";
    $past = ($diff > 0) ? "ago" : "";
    $return = $verb.$return.$past;
    return $return;
}

//echo "$cbcount|$cbcount_live|$cbcount_today";
$APIResult = array( "result" => "success", "data" => array( "callback_count" => $cbcount, "all_callbacks" => $cb_all, "callback_live" => $cbcount_live, "live_callbacks" => $cb_live, "callback_today" => $cbcount_today, "today_callbacks" => $cb_today ));
?>