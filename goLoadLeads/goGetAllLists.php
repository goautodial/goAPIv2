<?php
 /**
 * @file 		goGetAllList.php
 * @brief 		API for Getting All Lists
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	include_once ("goAPI.php");
	
    $limit = $astDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser, $astDB);
    
    if (!checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else {
		$ul = "WHERE user_group='$groupId'";
    }

	$query = "SELECT list_id,list_name,list_description,(SELECT count(*) AS tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) AS tally,active,list_lastcalldate,campaign_id,reset_time FROM vicidial_lists $wherecampaigns ORDER BY list_id LIMIT $limit";
	$rsltv = $astDB->rawQuery($query);
	foreach ($rsltv as $fresults){
		$dataListId[] =  $fresults['list_id'];
		$dataListName[] =  $fresults['list_name'];
		$dataActive[] =  $fresults['active'];
		$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
		$dataTally[] =  $fresults['tally'];
		$dataCampaignId[] =  $fresults['campaign_id'];
		
		$apiresults = array( "result" => "success", "list_id" => $dataListId, "list_name" => $dataListName, "active" => $dataActive, "list_lastcalldate" => $dataListLastcallDate, "tally" => $dataTally, "campaign_id" => $dataCampaignId);
	}
?>