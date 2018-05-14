<?php
/**
 * @file        goGetAllLists.php
 * @brief       API to get all lists
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

    $groupId = go_get_groupid($goUser, $astDB);
	$user_group = $astDB->escape($_REQUEST['user_group']);
    
    if (!checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
		$ul = "WHERE user_group='$groupId'";  
    }
	
	if (isset($user_group) && strlen($user_group) > 0) {
		//$query = "SELECT TRIM(allowed_campaigns) AS allowed_camps FROM vicidial_user_groups WHERE user_group='$user_group';";
		$astDB->where('user_group', $user_group);
		$rsltv = $astDB->getOne('vicidial_user_groups', 'TRIM(allowed_campaigns) AS allowed_camps');
		
		$allowed_campaigns = "";
		if (!preg_match("/ALL-CAMPAIGNS/", $rsltv['allowed_camps'])) {
			$allowed_camps = explode(' ', $rsltv['allowed_camps']);
			if (count($allowed_camps) > 0) {
				//$allowed_campaigns = ($ul !== '') ? "AND campaign_id IN (" : "WHERE campaign_id IN (";
				$allowed_campaigns = "WHERE campaign_id IN (";
				foreach ($allowed_camps as $camp) {
					if ($camp !== "-") {
						$allowed_campaigns .= "'{$camp}',";
					}
				}
				$allowed_campaigns = rtrim($allowed_campaigns, ",");
				$allowed_campaigns .= ")";
			}
		}
		
		$query = "SELECT vicidial_lists.list_id,vicidial_lists.list_name,vicidial_lists.list_description,(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,(SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count, vicidial_lists.active,vicidial_lists.list_lastcalldate,vicidial_lists.campaign_id,vicidial_lists.reset_time, vicidial_campaigns.campaign_name from vicidial_lists LEFT JOIN vicidial_campaigns ON vicidial_lists.campaign_id=vicidial_campaigns.campaign_id $allowed_campaigns order by list_id;";
		$rsltv = $astDB->rawQuery($query);
		foreach ($rsltv as $fresults) {
			$dataListId[] =  $fresults['list_id'];
			$dataListName[] =  $fresults['list_name'];
			$dataActive[] =  $fresults['active'];
			$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
			$dataTally[] =  $fresults['tally'];
			$dataCFCount[] =  $fresults['cf_count'];
			$dataCampaignId[] =  $fresults['campaign_id'];
			$dataCampaignName[] = $fresults['campaign_name'];
		}
		
		#get next list id
		//$query2 = "SELECT list_id from vicidial_lists WHERE list_id NOT IN ('999', '998') order by list_id;";
		$astDB->where('list_id', array('999','998'), 'not in');
		$astDB->orderBy('list_id', 'desc');
		$rsltv2 = $astDB->get('vicidial_lists', null, 'list_id');
		foreach ($rsltv2 as $fetch_lists){
			$lists[] =  $fetch_lists['list_id'];
		}
		
		$max_list = max($lists);
		$min_list = min($lists);
		
		if($max_list >= 99999999){
			for($i=1;$i < $max_list;$i++){
				if(!in_array($i, $lists['list_id'])){
					$next_list = $i;
					$i = $max_list;
				}
			}
		}else{
			$next_list = $max_list + 1;
		}
		
		$apiresults = array("result" => "success","list_id" => $dataListId,"list_name" => $dataListName,"active" => $dataActive, "list_lastcalldate" => $dataListLastcallDate,"tally" => $dataTally,"cf_count" => $dataCFCount,"campaign_id" => $dataCampaignId, "next_listID" => $next_list, "campaign_name" => $dataCampaignName);
	}else{
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}
	
	
?>
