<?php
 /**
 * @file 		goGetAllowedCampaigns.php
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

$hasLocation = $astDB->escape($_REQUEST['has_location']);

$agent = get_settings('user', $astDB, $goUser);
$camp_list = array();

$astDB->where('extension', $agent->phone_login);
$astDB->where('active', 'Y');
$checkPhone = $astDB->getOne('phones');
$phoneExist = $astDB->getRowCount();

if ($phoneExist > 0) {
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
} else {
    $APIResult = array( "result" => "error", "message" => "Phone login not configured" );
}
?>