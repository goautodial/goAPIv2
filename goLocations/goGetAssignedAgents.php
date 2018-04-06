<?php
 /**
 * @file 		goGetAssignedAgents.php
 * @brief 		API for Getting Assigned Agents - Locations
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

if (isset($_GET['location_id'])) { $location_id = $astDB->escape($_GET['location_id']); }
    else if (isset($_POST['location_id'])) { $location_id = $astDB->escape($_POST['location_id']); }
if (isset($_GET['agent'])) { $agent = $astDB->escape($_GET['agent']); }
    else if (isset($_POST['agent'])) { $agent = $astDB->escape($_POST['agent']); }
if (isset($_GET['active'])) { $active = $astDB->escape($_GET['active']); }
    else if (isset($_POST['active'])) { $active = $astDB->escape($_POST['active']); }
if (isset($_GET['campaign_id'])) { $campaign_id = $astDB->escape($_GET['campaign_id']); }
    else if (isset($_POST['campaign_id'])) { $campaign_id = $astDB->escape($_POST['campaign_id']); }

if (!is_null($location_id) && $location_id !== '') {
	$astDB->where('gc.location_id', $location_id);
}
if (!is_null($agent) && $agent !== '') {
	$astDB->where('vu.user', $agent);
}
if (!is_null($active) && $active !== '') {
	$astDB->where('vu.active', $active);
}
if (!is_null($campaign_id) && $campaign_id !== '') {
	$astDB->where('vc.campaign_id', $campaign_id);
}
$astDB->where('vu.user_group', 'AGENTS');
$astDB->orderBy('vc.campaign_id');
$astDB->groupBy('campaign');
$astDB->join('vicidial_campaign_agents vca', 'vu.user=vca.user', 'LEFT');
$astDB->join('vicidial_campaigns vc', 'vca.campaign_id=vc.campaign_id', 'LEFT');
$astDB->join("`$VARDBgo_database`.go_campaigns AS gc", 'gc.campaign_id=vc.campaign_id', 'LEFT');
$rsltv = $astDB->get('vicidial_users vu', null, 'vu.user_id,vu.user,vu.full_name,vca.campaign_id AS campaign, vc.campaign_name, vca.id AS campaign_agent, user_level AS role');

$dataUserID = [];
$dataName = [];
$dataFullName = [];
$dataCampaign = [];
$dataCampaignName = [];
$dataCampaignAgent = [];
$dataRole = [];
if (count($rsltv) > 0) {
	foreach ($rsltv as $row) {
		$dataUserID[] = $row['user_id'];
		$dataName[] = $row['user'];
		$dataFullName[] = $row['full_name'];
		$dataCampaign[] = $row['campaign'];
		$dataCampaignName[] = $row['campaign_name'];
		$dataCampaignAgent[] = $row['campaign_agent'];
		$dataRole[] = $row['role'];
	}
}
$APIResult = array("result" => "success", "user_id" => $dataUserID, "user" => $dataName, "full_name" => $dataFullName, "campaign" => $dataCampaign, "campaign_name" => $dataCampaignName, "campaign_agent" => $dataCampaignAgent, "role" => $dataRole);
?>