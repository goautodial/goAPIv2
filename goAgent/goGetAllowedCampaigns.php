<?php
####################################################
#### Name: goGetAllowedCampaigns.php            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$hasLocation = $astDB->escape($_REQUEST['has_location']);

$agent = get_settings('user', $astDB, $goUser);

// Getting Allowed Campigns
$astDB->where('user_group', $agent->user_group);
$query = $astDB->getOne('vicidial_user_groups', "REPLACE(TRIM(allowed_campaigns),' -','') AS allowed_campaigns");

// Get Campaign List
if (!preg_match("/ALL-CAMPAIGNS/", $query['allowed_campaigns'])) {
    $cl = str_replace(" ", "','", $query['allowed_campaigns']);
    $allowed_camps = "campaign_id IN ('$cl') AND";
}

if ($hasLocation) {
    $astDB->where('user', $goUser);
    $query = $astDB->get('vicidial_campaign_agents', null, 'campaign_id');
    $camps = array();
    foreach ($query as $row) {
        $camps[] = $row['campaign_id'];
    }
    $camps = implode("','", $camps);
    
    $allowed_camps = "campaign_id IN ('$camps') AND";
}

$result = $astDB->rawQuery("SELECT campaign_id,campaign_name FROM vicidial_campaigns WHERE $allowed_camps active='Y' AND (campaign_vdad_exten NOT IN ('8366', '8373') OR survey_method='AGENT_XFER') ORDER BY campaign_id");
//$camp_list = "<option value=''>".$lh->translationFor('select_a_campaign')."</option>";
foreach ($result as $camp) {
    //$camp_list .= "<option value='{$camp['campaign_id']}'>{$camp['campaign_name']}</option>";
    $camp_list[$camp['campaign_id']] = $camp['campaign_name'];
}

if (count($camp_list)) {
    ksort($camp_list);
    $APIResult = array( "result" => "success", "data" => array("allowed_campaigns" => $camp_list) );
} else {
    $APIResult = array( "result" => "error", "message" => "No allowed campaigns" );
}
?>