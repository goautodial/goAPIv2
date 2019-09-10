<?php
/**
 * @file        goGetStatisticalReports.php
 * @brief       API for Campaign Statistics
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

    // need function go_sec_convert();
    $fromDate 				= $astDB->escape($_REQUEST['fromDate'])." 00:00:00";
    $toDate 				= $astDB->escape($_REQUEST['toDate'])." 23:59:59";
    $campaignID				= $astDB->escape($_REQUEST['campaignID']);
    $request 				= $astDB->escape($_REQUEST['request']);
    //dispo_stats 			= $astDB->escape($_REQUEST['statuses']);
	
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
			$ul = "AND us.user_group = '$log_group'";
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
                        }else{
                                $array_camp[] = $campaignID;
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

		if (strtolower($request) == "outbound") {
			// Outbound Sales //
			$outbound_query = "
				SELECT us.full_name AS full_name, us.user AS user, 
				SUM(if (vlog.status REGEXP '^(".$statusRX.")$', 1, 0)) AS sale 
				FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl 
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
				AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0' 
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '".$fromDate."' AND '".$toDate."' 
				AND vlog.campaign_id IN ($imploded_camp) $ul 
				GROUP BY us.full_name;
			";
			$outbound_sql = $astDB->rawQuery($outbound_query);
			
			$totagents = $astDB->count;	
			if ($totagents > 0) {
				$total_sales				= 0;
				
				foreach($outbound_sql as $row){	
				//while ($row = $astDB->rawQuery($outbound_query)) {
					$TOPsorted_output	 	.= "<tr>";
					$TOPsorted_output 		.= "<td nowrap>".$row['full_name']."</td>";
					$TOPsorted_output 		.= "<td nowrap>".$row['user']."</td>";
					$TOPsorted_output		.= "<td nowrap>".$row['sale']."</td>";
					$TOPsorted_output 		.= "</tr>";
					$total_out_sales		 = $total_out_sales+$row['sale'];							
				}
			}
		}
		if (strtolower($request) == "inbound") {
			//GET ALL CLOSER CAMAPIGNS
			$closer_camps = go_getall_closer_campaigns("ALL", $astDB);
	
			$campaign_inb_query = "vlog.campaign_id IN ($closer_camps)";
				
			$query = "
				SELECT us.full_name AS full_name, us.user AS user, 
				SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
				FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl 
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
				AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0'  
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
				AND $campaign_inb_query $ul 
				GROUP BY us.full_name
				";
				
			if ($query) {
				$total_sales				= 0;
					
				foreach($astDB->rawQuery($query) as $row) {
				//while ($row = $astDB->rawQuery($inbound_query)) {
					$BOTsorted_output 		.= "<tr>";
					$BOTsorted_output 		.= "<td nowrap> ".$row['full_name']." </td>";
					$BOTsorted_output 		.= "<td nowrap> ".$row['user']." </td>";
					$BOTsorted_output 		.= "<td nowrap> ".$row['sale']." </td>";
					$BOTsorted_output 		.= "</tr>";
					$total_in_sales 		= $total_in_sales + $row['sale'];
				}
			}
		}
		$apiresults = array(
			"result"		=> "success",
			"TOPsorted_output"	=> $TOPsorted_output, 
			"BOTsorted_output"	=> $BOTsorted_output, 
			"TOToutbound" 		=> $total_out_sales, 
			"TOTinbound" 		=> $total_in_sales,
			"TOTAgents"		=> $totagents
	);
			
			return $apiresults;
	}

?>
