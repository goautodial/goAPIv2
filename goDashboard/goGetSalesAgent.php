<?php
/**
 * @file        goGetSalesAgent.php
 * @brief       API for Sales Agent Report on Dashboard for Statewide
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Thom Bernarth D. Patacsil
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

    $fromDate = "";
    $toDate = "";
    $campaignID				= $astDB->escape($_REQUEST['campaign_id']);
    $campaignID				= (!empty($campaignID) ? $campaignID : 'ALL');
	
    if (empty($fromDate)) {
    	$fromDate			= date("Y-m-d")." 00:00:00";
    }
    
    if (empty($toDate)) {
    	$toDate 			= date("Y-m-d")." 23:59:59";
    }
		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults = array(
			"result" => "Error: Session User Not Defined."
		);
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg = error_handle("40001");
        	$apiresults = array(
			"code" => "40001",
			"result" => $err_msg
		);
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate = date("Y-m-d") . " 00:00:00";
		$toDate = date("Y-m-d") . " 23:59:59";
	} else {            
		// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
		// every time we need to filter out requests
		$tenant	= (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
		
		if ($tenant) {
			$astDB->where("user_group", $log_group);
		} else {
			if (strtoupper($log_group) != 'ADMIN') {
				if ($user_level > 8) {
					$astDB->where("user_group", $log_group);
				}
			}
		}
			
		// SALES PER AGENT
		if ($log_group !== "ADMIN") {
			$ul = "AND vlog.user_group = '$log_group'";
            if ($log_group === "AGENT") {
                $ul .= " AND vlog.user = '$log_user'";
            }
		} else {
			$ul = "";
		}
		
		$Qstatus = $astDB
			->where("sale", "Y")
			->get("vicidial_statuses", NULL, "status");
				
		$sstatusRX = "";
		$sstatuses = array();			
		$a = 0;
			
		if ($astDB->count > 0) {
			foreach ($Qstatus as $rowQS) {
				$goTempStatVal = $rowQS['status'];
				$sstatuses[$a] = $rowQS['status'];
				$sstatusRX .= "{$goTempStatVal}|";
				$a++;
			}			
		}
			
		//if (!empty($sstatuses))
			$sstatuses = implode("','",$sstatuses);
		
		//ALL CAMPAIGNS
			if ("ALL" === strtoupper($campaignID)) {
				$SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");

				foreach($SELECTQuery as $camp_val){
					$array_camp[] = $camp_val["campaign_id"];
				}

				$SELECTQueryList = $astDB->get("vicidial_lists", null, "list_id");
				$array_list = $SELECTQueryList;

			}else{
				$array_camp[] = $campaignID;

				$i = 0;
				foreach($array_camp as $camp) {
					$camp_id = $array_camp[$i];
					$astDB->WHERE("campaign_id", $camp_id);
					$SELECTQuery = $astDB->get("vicidial_lists", null, "list_id");
					$array_list = $SELECTQuery;
					$i++;
				}
			}

		foreach($array_list as $list){
			$custom_list_id = "custom_" . (!empty($list['list_id']) ? $list['list_id'] : $list);
			$query_CF_list = $astDB->rawQuery("DESC {$custom_list_id};");
			if ($query_CF_list) {
				foreach ($query_CF_list as $field_list) {
					$exec_query_CF_list = $field_list["Field"];

					if ($exec_query_CF_list == "Amount") {
						$array_list_amount[] = $custom_list_id;
					}
				}
			}
		}

		$imploded_camp = "'".implode("','", $array_camp)."'";		
	
		$Qstatus2 = $astDB
			->where("sale", "Y")
			->where("campaign_id", $array_camp, "IN")
			->get("vicidial_campaign_statuses", NULL, "status");
			
		$cstatusRX = "";
		$cstatuses = array();			
		$b = 0;
			
		if ($astDB->count > 0) {
			foreach ($Qstatus2 as $rowQS2) {
				$goTempStatVal = $rowQS2['status'];
				$cstatuses[$b] = $rowQS2['status'];
				$cstatusRX .= "{$goTempStatVal}|";
				$b++;
			}			
		}
			
		//if (!empty($cstatuses)) {
			$cstatuses = implode("','",$cstatuses);
		//}
			
		if (count($sstatuses) > 0 && count($cstatuses) > 0) {
			$statuses = "{$sstatuses}','{$cstatuses}";
			$statusRX = "{$sstatusRX}{$cstatusRX}";
		} else {
			$statuses = (count($sstatuses) > 0 && count($cstatuses) < 1) ? $sstatuses : $cstatuses;
			$statusRX = (count($sstatusRX) > 0 && count($cstatusRX) < 1) ? $sstatusRX : $cstatusRX;
		}
			
		$statusRX = trim($statusRX, "|");

		$TOPsorted_output                               = "";
		$BOTsorted_output                               = "";
		$total_in_sales                                 = "";
		$total_out_sales                                = "";
		$total_out_sales_amount				= 0;

		$closer_camps = go_getall_closer_campaigns("ALL", $astDB);
		$campaign_inb_query = "vlog.campaign_id IN ($closer_camps)";

		$query = "
			SELECT t.full_name AS full_name, t.user AS user, SUM(t.sale) AS sale FROM (
				SELECT us.full_name AS full_name, us.user AS user, 
				SUM(if (vlog.status REGEXP '^(".$statusRX.")$', 1, 0)) AS sale 
				FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl 
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
				AND vl.lead_id = vlog.lead_id 
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '".$fromDate."' AND '".$toDate."' 
				AND vlog.campaign_id IN ($imploded_camp) $ul 
				GROUP BY us.full_name
				UNION
				SELECT us.full_name AS full_name, us.user AS user, 
				SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
				FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl 
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
				AND vl.lead_id = vlog.lead_id  
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
				AND $campaign_inb_query $ul 
				GROUP BY us.full_name 
			) t GROUP BY t.full_name;
		";
		$sql = $astDB->rawQuery($query);

		$outbound_select_query_sales = "";
		$i = 0;
		
		foreach($array_list_amount as $list_amount){
			if($i > 0) {
				$outbound_select_query_sales .= " UNION ";
			} 

			$outbound_select_query_sales .= "
				SELECT vlog.user, vlog.status, sum(IFNULL(cf.Amount, 0)) AS Amount 
				FROM vicidial_log vlog 
				LEFT JOIN $list_amount cf on vlog.lead_id = cf.lead_id 
				WHERE vlog.lead_id=cf.lead_id 
				AND vlog.status IN ('$statuses') 
				AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
				AND vlog.campaign_id IN ($imploded_camp) $ul
				GROUP BY vlog.user
				";	
			$i++;
		}

		$inbound_select_query_sales = "";
		$i = 0;

		foreach($array_list_amount as $list_amount){
			if($i == 0) {
				if($outbound_select_query_sales != ''){
					$inbound_select_query_sales .= " UNION ";
				}
			}

			if($i > 0) {
				$inbound_select_query_sales .= " UNION ";
			}

			$inbound_select_query_sales .= "
				SELECT vlog.user, vlog.status, sum(IFNULL(cf.Amount, 0)) AS amount
				FROM vicidial_closer_log vlog
				LEFT JOIN $list_amount cf on vlog.lead_id = cf.lead_id
				WHERE vlog.lead_id=cf.lead_id
				AND vlog.status IN ('$statuses')
				AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
				AND $campaign_inb_query $ul
				GROUP BY vlog.user
				";
			$i++;
		}

		if($outbound_select_query_sales != '' || $inbound_select_query_sales != '' ){
			$col_exists = 1;
		} else {
			$col_exists = 0;
		}

		if($col_exists){
			$query_sales = "SELECT t.user, t.status, sum(IFNULL(t.amount, 0)) AS amount 
				FROM ( $outbound_select_query_sales $inbound_select_query_sales ) t 
				GROUP BY t.user";

			$sql_sales = $astDB->rawQuery($query_sales);
		}

		$totagents = $astDB->count;	

		$apiresults = array(
			"result"			=> "success",
			"sales"				=> $sql, 
			"amount"			=> $sql_sales,
			"TOTagents"			=> $totagents,
			"col_exists"		=> $col_exists,
            "test_sql"          => $query_sales
		);
			
		return $apiresults;
	}

?>
