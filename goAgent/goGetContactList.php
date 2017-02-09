<?php
####################################################
#### Name: goGetContactList.php                 ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$is_logged_in = check_agent_login($astDB, $goUser);
$agent = get_settings('user', $astDB, $goUser);

if (isset($_GET['goLimit'])) { $limit = $astDB->escape($_GET['goLimit']); }
    else if (isset($_POST['goLimit'])) { $limit = $astDB->escape($_POST['goLimit']); }

if (!isset($limit) || !is_numeric($limit)) {
    $limit = 10000;
}

$astDB->where('user_group', $agent->user_group);
$rslt = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
$allowed_campaigns = trim(preg_replace("/\ -$/", "", $rslt['allowed_campaigns']));

if (!$is_logged_in) {
    if (!preg_match("/ALL-CAMPAIGNS/", $allowed_campaigns)) {
        $camp_array = preg_split("/[\s,]+/", $allowed_campaigns);
        $astDB->where('campaign_id', $camp_array, 'in');
    }
} else {
    $astDB->where('campaign_id', $campaign);
}
$astDB->where('active', 'Y');
$rslt = $astDB->get('vicidial_lists', null, 'list_id');
$list_ids = array();
foreach ($rslt as $val) {
    $list_ids[] = $val['list_id'];
}

if (count($list_ids) > 0 ) {
    $astDB->where('vl.list_id', $list_ids, 'in');
    $astDB->where('vl.status', array('DNC', 'DNCL'), 'not in');
    $astDB->join('vicidial_lists vls', 'vls.list_id=vl.list_id', 'left');
    $rslt = $astDB->get('vicidial_list vl', $limit, 'lead_id,first_name,middle_initial,last_name,phone_number,last_local_call_time,campaign_id,status,comments,phone_code');

    foreach ($rslt as $lead) {
        $leads[] = $lead;
    }

    $APIResult = array( 'result' => 'success', 'leads' => $leads, 'test' => $astDB->getLastQuery() );
} else {
    $APIResult = array( 'result' => 'error', 'message' => 'No leads found.' );
}
?>