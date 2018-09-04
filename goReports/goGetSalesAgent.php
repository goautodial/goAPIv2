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
	
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB);
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);

	// need function go_sec_convert();
    $pageTitle 										= strtolower($astDB->escape($_REQUEST['pageTitle']));
    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaign_id 									= $astDB->escape($_REQUEST['campaignID']);
    $request 										= $astDB->escape($_REQUEST['request']);
	//$dispo_stats 									= $astDB->escape($_REQUEST['statuses']);
	
    if (empty($fromDate)) {
    	$fromDate 									= date("Y-m-d")." 00:00:00";
	}
    
    if (empty($toDate)) {
    	$toDate 									= date("Y-m-d")." 23:59:59";
	}
		
	$defPage 										= array(
		"stats", 
		"agent_detail", 
		"agent_pdetail", 
		"dispo", 
		"call_export_report", 
		"sales_agent", 
		"sales_tracker", 
		"inbound_report"
	);

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif ( empty($campaign_id) || is_null($campaign_id) ) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
		//die($fromDate." - ".$toDate);									=> $err_msg
	} elseif (!in_array($pageTitle, $defPage)) {
	 	$err_msg 									= error_handle("10004");
		$apiresults 								= array(
			"code" 										=> "10004", 
			"result" 									=> $err_msg
		);
	} else {            
		// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
		// every time we need to filter out requests
		$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
		
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
		if ($pageTitle == "sales_agent") {
			if ($log_group !== "ADMIN") {
				$ul 							= "AND us.user_group = '$log_group'";
			} else {
				$ul 							= "";
			}
			
			if ($request == "outbound") {
				// Outbound Sales //					
				$outbound_query 				= "
					SELECT us.full_name AS full_name, us.user AS user, 
						SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
					FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl 
					WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
					AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0' 
					AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					AND vlog.campaign_id = '$campaignID' $ul 
					GROUP BY us.full_name
				";
				
				$TOPsorted_output 				= "";
				$total_out_sales 				= "";
				
				if ($query) {
					$total_sales				= 0;
					
					while ($row = $astDB->rawQuery($outbound_query)) {
						$TOPsorted_output	 	.= "<tr>";
						$TOPsorted_output 		.= "<td nowrap>".$row['full_name']."</td>";
						$TOPsorted_output 		.= "<td nowrap>".$row['user']."</td>";
						$TOPsorted_output		.= "<td nowrap>".$row['sale']."</td>";
						$TOPsorted_output 		.= "</tr>";
						$total_out_sales		 = $total_out_sales+$row['sale'];							
					}
				}
			}
			
			if ($request == "inbound") {
				// Inbound Sales //
				$inbound_query 					= "
					SELECT closer_campaigns FROM vicidial_campaigns 
					WHERE campaign_id='".$campaignID."' 
					ORDER BY campaign_id
				";
				
				$row1 							= $astDB->rawQuery($inbound_query);
				$closer_camp_array				= explode(" ",$row1['closer_campaigns']);
				$num 							= count($closer_camp_array);				
				$x								= 0;
				
				while ($x<$num) {
					if ($closer_camp_array[$x]!="-") {
						$closer_campaigns[$x] 	= $closer_camp_array[$x];
					}
					
					$x++;
				}
				
				$campaign_inb_query				= "vlog.campaign_id IN ('".implode("','",$closer_campaigns)."')";
				
				$query 							= "
					SELECT us.full_name AS full_name, us.user AS user, 
						SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
						FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl 
						WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
						AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0'  
						AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
						AND $campaign_inb_query $ul 
						GROUP BY us.full_name
					";
				
				$BOTsorted_output 				= "";
				$total_in_sales 				= "";
				
				if ($query) {
					$total_sales				= 0;
					
					//foreach($query->result() as $row) {
					while ($row = $astDB->rawQuery($inbound_query)) {
						$BOTsorted_output 		.= "<tr>";
						$BOTsorted_output 		.= "<td nowrap> ".$row['full_name']." </td>";
						$BOTsorted_output 		.= "<td nowrap> ".$row['user']." </td>";
						$BOTsorted_output 		.= "<td nowrap> ".$row['sale']." </td>";
						$BOTsorted_output 		.= "</tr>";
						$total_in_sales 		= $total_in_sales + $row['sale'];
					}
				}
			}
			
			$apiresults 						= array(
				"TOPsorted_output" 					=> $TOPsorted_output, 
				"BOTsorted_output" 					=> $BOTsorted_output, 
				"TOToutbound" 						=> $total_out_sales, 
				"TOTinbound" 						=> $total_in_sales, 
				"query" 							=> $outbound_query
			);
			
			return $apiresults;
		}
	}

?>
