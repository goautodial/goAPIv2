<?php
####################################################
#### Name: goGetLabels.php                      ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goLimit'])) { $limit = $astDB->escape($_GET['goLimit']); }
    else if (isset($_POST['goLimit'])) { $limit = $astDB->escape($_POST['goLimit']); }
if (isset($_GET['goDate'])) { $date = $astDB->escape($_GET['goDate']); }
    else if (isset($_POST['goTabgoDateleName'])) { $date = $astDB->escape($_POST['goDate']); }

if (!isset($limit) || strlen($limit) < 1 || !is_numeric($limit)) {
	$limit = 5;
}
if (!isset($date) || (isset($date) && strlen($date) !== 10)) {
	$date = date("Y-m-d");
}

$statuses = array();
$rslt = $astDB->get('vicidial_statuses', null, 'status,status_name');
foreach ($rslt as $row) {
	$status = $row['status'];
	$statuses[$status] = $row['status_name'];
}

$astDB->where('campaign_id', $campaign);
$rslt = $astDB->get('vicidial_campaign_statuses', null, 'status,status_name');
foreach ($rslt as $row) {
	$status = $row['status'];
	$statuses[$status] = $row['status_name'];
}

$astDB->where('campaign_id', $campaign);
$astDB->where('user', $goUser);
$astDB->where('event_time', array("$date 00:00:00", "$date 23:59:59"), 'between');
$astDB->where('talk_sec', '0', '>');
$astDB->orderBy('event_time', 'desc');
$rslt = $astDB->get('vicidial_agent_log', $limit, 'event_time AS time,lead_id,status');

$return = array();
foreach ($rslt as $row) {
	$astDB->where('lead_id', $row['lead_id']);
	$rslt2 = $astDB->getOne('vicidial_list', 'first_name,last_name');
	$full_name = trim($rslt2['first_name']." ".$rslt2['last_name']);
	
	$status = $row['status'];
	$row['name'] = $full_name;
	$row['status'] = $statuses[$status];
	unset($row['lead_id']);
	$return[] = $row;
}

$APIResult = array( "result" => "success", "data" => $return );
?>