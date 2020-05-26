<?php
/**
 * @file        goExportCallReport.php
 * @brief       API for Agent Time Details Reports
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja 
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it AND/or modify
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
    
	include_once("goAPI.php");
	
	$campaigns 		= $astDB->escape($_REQUEST['campaigns']);
	$inbounds 		= $astDB->escape($_REQUEST['inbounds']);
	$lists 			= $astDB->escape($_REQUEST['lists']);
	$dispo_stats            = $astDB->escape($_REQUEST['statuses']);
	$custom_fields 	= $astDB->escape($_REQUEST['custom_fields']);
	$per_call_notes = $astDB->escape($_REQUEST['per_call_notes']);
	$rec_location 	= $astDB->escape($_REQUEST['rec_location']);
	$log_group 		= go_get_groupid($session_user, $astDB);
	$fromDate = $astDB->escape($_REQUEST['fromDate']);        
	$toDate = $astDB->escape($_REQUEST['toDate']);
	
	$limit = $astDB->escape($_REQUEST['limit']);
	$offset = $astDB->escape($_REQUEST['offset']);

	if (empty($fromDate))
		$fromDate = date("Y-m-d")." 00:00:00";
	if (empty($toDate)) 
		$toDate = date("Y-m-d")." 23:59:59";
        
	if (!empty($campaigns) && $campaigns != NULL)
	    $campaigns = explode(",",$campaigns);
	if (!empty($inbounds))
	    $inbounds = explode(",",$inbounds);
	if (!empty($lists))	
	    $lists = explode(",",$lists);
	if (!empty($dispo_stats))	
	    $dispo_stats = explode(",",$dispo_stats);
	
	$campaign_SQL = "";
	$group_SQL = "";
	$list_SQL = "";
	$status_SQL = "";

	$campaign_ct = count($campaigns);
	$group_ct = count($inbounds);
	$list_ct = count($lists);
	$status_ct = count($dispo_stats);

	if (!empty($campaigns)) {
		$fresults = $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess = $astDB->getRowCount();
		$userlevel = $fresults["user_level"];

		$i = 0;
		//$array_campaign = Array();

		while ($i < $campaign_ct) {
			//$campaign_SQL .= "'$campaigns[$i]',";
			$campaign_SQL .= "'$campaigns[$i]',";
			//array_push($array_campaign, $campaigns[$i]);
			$i++;
		}
		
		if (in_array("ALL", $campaigns)) {
			$campaign_SQL = "";
			$i = 0;
			$SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");
			$campaign_ct = $astDB->count;
			foreach($SELECTQuery as $camp_val){
				$array_camp[] = $camp_val["campaign_id"];
			}
			$imp_camp = implode("','", $array_camp);
			if (strtoupper($log_group) !== 'ADMIN') {
				if ($log_group !== 'SUPERVISOR') {
					$campaign_SQL = "AND vl.campaign_id IN('$imp_camp')";
				}
			}
			//die("ALEX");	
                }else{
			$campaign_SQL = preg_replace("/,$/i",'',$campaign_SQL);
			$campaign_SQL = "AND vl.campaign_id IN($campaign_SQL)";
		}

		$RUNcampaign = 1;
		
	} else {
		$RUNcampaign = 0;
	}
	
	if (!empty($inbounds)) {
		$i=0;
		if (in_array("ALL", $inbounds)) {
			$group_SQL = go_getall_closer_campaigns("ALL", $astDB);
			$i=1;
		} else {
			$i = 0;
			//$array_inbound 							= Array();

			while ($i < $group_ct) {
				if (strlen($inbounds[$i]) > 0) {
				  //$group_SQL .= "'$inbounds[$i]',";
					$group_SQL .= "'$inbounds[$i]',";
					//array_push($array_inbound, $inbounds[$i]);
				}
				$i++;
			}
		
			$group_SQL 								= preg_replace("/,$/i",'',$group_SQL);
		}
		if ($group_ct > 0) {
			$group_SQL 							= "AND vcl.campaign_id IN($group_SQL)";
		}
		
		$RUNgroup								=$i;
	} else {
		$RUNgroup								= 0;
	}
	
	if (!empty($lists)) {
		//$list_SQL 								= "";
		$list_SQL								= implode("','", $lists);
		
		//$i										= 0;
		//$array_list 							= Array();
		//while ($i < $list_ct) {
		//	//$list_SQL .= "'$lists[$i]',";
		//	$list_SQL 							.= "'$lists[$i]',";
		//	//array_push($array_list, $lists[$i]);
		//	$i++;
		//}
		
		if (in_array("ALL", $lists)) {
			$list_SQL 							= "";
			
			if (in_array("ALL", $campaigns)) {
				$SELECTQuery = $astDB->get("vicidial_lists", null, "list_id");
				$array_list = $SELECTQuery;
			} else {
				$i									= 0;
				while ($i < $campaign_ct) {
					$camp_id = $campaigns[$i];
					$astDB->WHERE("campaign_id", $camp_id);
					$SELECTQuery = $astDB->get("vicidial_lists", null, "list_id");
					//$query_list = mysqli_query($astDB,"SELECT list_id FROM vicidial_lists WHERE campaign_id = '$camp_id';");
					$array_list = $SELECTQuery;
					
					$i++;
				}
			}
		}
		else{
			//$list_SQL 							= preg_replace("/,$/i",'',$list_SQL);
			$list_SQL 							= "AND vi.list_id IN('$list_SQL')";
			$array_list							= $lists;
			//$i									= 0;
			//
			//while ($i < $list_ct) {
			//	$array_list[] 					= $lists[$i];
			//	$i++;
			//}
		}
	}
	
	if (!empty($dispo_stats)) {
		$i= 0;
		//$array_status 							= Array();

		while ($i < $status_ct) {
			//$status_SQL .= "'$dispo_stats[$i]',";
			$status_SQL 						.= "'$dispo_stats[$i]',";
			//array_push($array_status, $dispo_stats[$i]);
			$i++;
		}
		
		if ( (in_array("ALL", $dispo_stats)) ) {
			$status_SQL 						= "";
		} else {
			$status_SQL 						= preg_replace("/,$/i",'',$status_SQL);
			$status_SQL 						= "AND vl.status IN ($status_SQL)";
		}
	}
	
	if ($log_group !== "ADMIN") {
		if ($log_group !== 'SUPERVISOR') {
			$stringv 								= go_getall_allowed_users($log_group);
			$user_group_SQL 						= "AND vl.user IN ($stringv)";
		} else {
			$user_group_SQL                                                 = "";
		}
	}  else{
		$user_group_SQL 						= "";
	}
	
	$export_fields_SQL 							= "";
	
	$duration_sql = "vl.length_in_sec as call_duration,";
	$duration_sql2 = "vcl.length_in_sec as call_duration,";

	if ($RUNcampaign > 0 && $RUNgroup < 1) {
		$query = "SELECT count(vl.user) as row_count
			FROM vicidial_users vu, vicidial_log vl,vicidial_list vi 
			WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') 
			AND vu.user=vl.user AND vi.lead_id=vl.lead_id 
			$list_SQL $campaign_SQL 
			$user_group_SQL $status_SQL 
			order by vl.call_date";
	}
	
	if ($RUNgroup > 0 && $RUNcampaign < 1) {
		$query	= "SELECT count(vl.user) as row_count
			FROM vicidial_users vu, vicidial_closer_log vcl, vicidial_list vi 
			WHERE (date_format(vcl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') 
			AND vu.user=vcl.user AND vi.lead_id=vcl.lead_id 
			AND vi.lead_id = vcl.lead_id 
			$list_SQL $group_SQL 
			$user_group_SQL $status_SQL 
			order by vcl.call_date";
	}
	if ($RUNcampaign > 0 && $RUNgroup > 0) {
		$query = "SELECT SUM(t.row_count) as row_count FROM
			((SELECT count(vl.user) as row_count
			FROM vicidial_users vu, vicidial_log vl,vicidial_list vi
			WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') 
			AND vu.user=vl.user AND vi.lead_id=vl.lead_id 
			$list_SQL 
			$campaign_SQL 
			$user_group_SQL 
			$status_SQL 
			order by vl.call_date
		 	) UNION (
			SELECT count(vcl.user) as row_count
			FROM vicidial_users vu, vicidial_closer_log vcl,vicidial_list vi 
			WHERE (date_format(vcl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') 
			AND vu.user=vcl.user AND vi.lead_id=vcl.lead_id  
			$list_SQL 
			$group_SQL 
			$user_group_SQL 
			$status_SQL 
			order by vcl.call_date)) t";
    }
	$results = $astDB->rawQuery($query);

	foreach($results as $result){
		$row_count = intval($result['row_count']);
	}
	
	$apiresults = array(
		"result" => "success", 
		"query" => $query,
		"row_count" => $row_count
	);
?>

