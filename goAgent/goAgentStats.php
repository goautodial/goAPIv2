<?php
 /**
 * @file 		goAgentStats.php
 * @brief 		API to get agent stats
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

if (isset($_GET['goUserID'])) { $user_id = $astDB->escape($_GET['goUserID']); }
    else if (isset($_POST['goUserID'])) { $user_id = $astDB->escape($_POST['goUserID']); }
if (isset($_GET['forCampaign'])) { $forCampaign = $astDB->escape($_GET['forCampaign']); }
    else if (isset($_POST['forCampaign'])) { $forCampaign = $astDB->escape($_POST['forCampaign']); }

$query_date = date('Y-m-d');
$dateSQL = "WHERE event_time BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";

$userSQL = "";
if (!empty($user_id)) {
	$userSQL = "AND val.user='$user_id'";
}

$campSQL = "";
if (isset($campaign) && $campaign !== '') {
	$campSQL = "AND val.campaign_id='$campaign'";
}

$query = "SELECT pause_sec , wait_sec , talk_sec , dispo_sec , dead_sec , vu.user_id
			FROM vicidial_agent_log val INNER JOIN vicidial_users vu ON vu.user=val.user $dateSQL $userSQL $campSQL";
$rslt = $astDB->rawQuery($query);

$totalHours = 0;
foreach ($rslt as $row) {
	$totalHours += ($row['pause_sec'] + $row['wait_sec'] + $row['talk_sec'] + $row['dispo_sec']);
}

$query = "SELECT val.agent_log_id, vu.user_id FROM vicidial_agent_log val
			INNER JOIN vicidial_users vu ON vu.user=val.user
			WHERE val.training_mode=0 AND (val.lead_id IS NOT NULL OR val.lead_id!=0) $dateSQL $userSQL $campSQL";
$rslt = $astDB->rawQuery($query);
$totalCalls = $this->getRowCount();

// Convert the calculcated hourse to float
$totalHours = round($totalHours / 3600 , 4);

if( isLeadCampaignDef($goDB, $campaign_id) ){
	$leadCampaign = " 'LeaS' ";
	$salesStatuses = " 'LeaS' ";
} else {
	$salesStatuses = gatAllStatusCodes($campaign_id , true);
}



function isLeadCampaignDef($thisDB, $campID){
	$rslt = $thisDB->rawQuery("SELECT cd.id as ID FROM campaign_definitions cd, go_campaigns gc WHERE gc.campaign_definition = cd.code AND gc.campaign_id = '$campaign'");

	if( $thisDB->getRowCount() ) {
		$camp_id = $rslt;
		if($camp_id['0']['ID'] == 5) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function gatAllStatusCodes($campID, $salesYes=false){
	$statuses = "";
	$cstatuses = "";
	$statuses = "";

	$salesQuery = ($salesYes) ? " sale='Y' AND " : "";

	$this->db->query("SELECT status FROM vicidial_statuses WHERE $salesQuery cam_category IN (SELECT vicidial_campaign_definitions.id FROM vicidial_campaign_definitions, vicidial_campaigns WHERE vicidial_campaigns.campaign_def = vicidial_campaign_definitions.code AND vicidial_campaigns.campaign_id = '$campID')");
	foreach($this->db->fetchRowAssociative() as $status)
	{
		$sstatuses[$status['status']] = $status['status'];
	}
	$sstatuses = implode("','", $sstatuses);
	$sstatuses = "'".$sstatuses."'";

	$this->db->query("SELECT vicidial_campaign_statuses.status FROM vicidial_campaign_statuses WHERE vicidial_campaign_statuses.sale='Y' AND vicidial_campaign_statuses.campaign_id = '$campID'");

	foreach($this->db->fetchRowAssociative() as $status)
	{
		$cstatuses[$status['status']] = $status['status'];
	}
	$cstatuses = implode("','", $cstatuses);
	$cstatuses = "'".$cstatuses."'";

	if(strlen($sstatuses) > 0 && strlen($cstatuses) > 0)
	{
		$statuses = "{$sstatuses}','{$cstatuses}";
	}
	else
	{
		$statuses = (strlen($sstatuses) > 0 && strlen($cstatuses) < 1) ? $sstatuses : $cstatuses;
	}

	return $statuses;
}

$APIResult = array( "result" => "success", "data" => array( "disable_alter_custphone" => $disable_alter_custphone, "labels" => $rslt ) );
?>