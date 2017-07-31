<?php
/*
#######################################################
#### Name: goGetAssignedAgents.php	               ####
#### Description: API to get all assigned agents   ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
#### Written by: Chris Lomuntad                    ####
#### License: AGPLv2                               ####
#######################################################
*/

if (isset($_GET['location'])) { $location = $astDB->escape($_GET['location']); }
    else if (isset($_POST['location'])) { $location = $astDB->escape($_POST['location']); }
if (isset($_GET['agent'])) { $agent = $astDB->escape($_GET['agent']); }
    else if (isset($_POST['agent'])) { $agent = $astDB->escape($_POST['agent']); }
if (isset($_GET['active'])) { $active = $astDB->escape($_GET['active']); }
    else if (isset($_POST['active'])) { $active = $astDB->escape($_POST['active']); }

//SELECT vicidial_users.user_id, vicidial_users.user, vicidial_users.full_name, vicidial_campaigns.campaign_id AS campaign, vicidial_campaigns.campaign_name, vicidial_campaign_agents.id AS campaignAgent
//FROM vicidial_users
//INNER JOIN vicidial_campaign_agents ON vicidial_users.user=vicidial_campaign_agents.user
//INNER JOIN vicidial_campaigns ON vicidial_campaign_agents.campaign_id = vicidial_campaigns.campaign_id
//WHERE vicidial_users.user_group='AGENTS' ORDER BY vicidial_campaigns.campaign_id;
$goDB->where('users.user_group', 'AGENTS');
$goDB->orderBy('go_campaigns.campaign_id');
$goDB->join('`asteriskV4`.vicidial_campaign_agents vca', 'users.user=vca.user', 'LEFT');
$goDB->join('go_campaigns', 'vca.campaign_id=go_campaigns.campaign_id');
$rsltv = $goDB->get('users', null, 'users.userid,users.name,users.fullname,vca.campaign_id AS campaign, go_campaigns.campaign_name, role');

$dataUserID = [];
$dataName = [];
$dataFullName = [];
$dataCampaign = [];
$dataCampaignName = [];
$dataRole = [];
foreach ($rsltv as $row) {
	$dataUserID[] = $row['userid'];
	$dataName[] = $row['name'];
	$dataFullName[] = $row['fullname'];
	$dataCampaign[] = $row['campaign'];
	$dataCampaignName[] = $row['campaign_name'];
	$dataRole[] = $row['role'];
}
$APIResult = array("result" => "success", "user_id" => $dataUserID, "user" => $dataName, "full_name" => $dataFullName, "campaign" => $dataCampaign, "campaign_name" => $dataCampaignName, "role" => $dataRole);

?>
