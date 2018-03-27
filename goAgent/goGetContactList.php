<?php
 /**
 * @file 		goGetContactList.php
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

$agent = get_settings('user', $astDB, $goUser);

if (isset($_GET['goLimit'])) { $limit = $astDB->escape($_GET['goLimit']); }
    else if (isset($_POST['goLimit'])) { $limit = $astDB->escape($_POST['goLimit']); }

if (isset($_GET['goLeadSearchMethod'])) { $agent_lead_search_method = $astDB->escape($_GET['goLeadSearchMethod']); }
    else if (isset($_POST['goLeadSearchMethod'])) { $agent_lead_search_method = $astDB->escape($_POST['goLeadSearchMethod']); }

if (isset($_GET['goIsLoggedIn'])) { $is_logged_in = $astDB->escape($_GET['goIsLoggedIn']); }
    else if (isset($_POST['goIsLoggedIn'])) { $is_logged_in = $astDB->escape($_POST['goIsLoggedIn']); }

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
    if (preg_match("/CAMPLISTS/", $agent_lead_search_method)) {
        $astDB->where('campaign_id', $campaign);
    } else {
        if (!preg_match("/ALL-CAMPAIGNS/", $allowed_campaigns)) {
            $camp_array = preg_split("/[\s,]+/", $allowed_campaigns);
            $astDB->where('campaign_id', $camp_array, 'in');
        }
    }
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

    $APIResult = array( 'result' => 'success', 'leads' => $leads );
} else {
    $APIResult = array( 'result' => 'error', 'message' => 'No leads found.' );
}
?>