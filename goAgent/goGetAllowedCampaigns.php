<?php
####################################################
#### Name: goGetAllowedCampaigns.php            ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$agent = get_settings('user', $astDB, $goUser);

// Getting Allowed Campigns
$astDB->where('user_group', $agent->user_group);
$query = $astDB->getOne('vicidial_user_groups', "REPLACE(TRIM(allowed_campaigns),' -','') AS allowed_campaigns");

// Get Campaign List
if (!preg_match("/ALL-CAMPAIGNS/", $query['allowed_campaigns'])) {
    //$cl = explode(' ', $query['allowed_campaigns']);
    //$astDB->where('campaign_id', $cl, 'in');
    $cl = str_replace(" ", "','", $query['allowed_campaigns']);
    $allowed_camps = "campaign_id IN ('$cl') AND";
}
//$astDB->where('active', 'Y');
//$astDB->where('campaign_vdad_exten', array('8366', '8373'), 'not in');
//$astDB->orWhere('survey_method', 'AGENT_XFER');
//$astDB->orderBy('campaign_id');
//$result = $astDB->get('vicidial_campaigns', null, 'campaign_id,campaign_name');
$result = $astDB->rawQuery("SELECT campaign_id,campaign_name FROM vicidial_campaigns WHERE $allowed_camps active='Y' AND (campaign_vdad_exten NOT IN ('8366', '8373') OR survey_method='AGENT_XFER') ORDER BY campaign_id");
//$camp_list = "<option value=''>".$lh->translationFor('select_a_campaign')."</option>";
foreach ($result as $camp) {
    //$camp_list .= "<option value='{$camp['campaign_id']}'>{$camp['campaign_name']}</option>";
    $camp_list[$camp['campaign_id']] = $camp['campaign_name'];
}

if (count($camp_list)) {
    ksort($camp_list);
    $APIResult = array( "result" => "success", "data" => array("allowed_campaigns" => $camp_list), "query" =>  "SELECT campaign_id,campaign_name FROM vicidial_campaigns WHERE $allowed_camps active='Y' AND (campaign_vdad_exten NOT IN ('8366', '8373') OR survey_method='AGENT_XFER') ORDER BY campaign_id");
} else {
    $APIResult = array( "result" => "error", "message" => "No allowed campaigns" );
}
?>