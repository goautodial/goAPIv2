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
    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaignID 									= $astDB->escape($_REQUEST['campaignID']);
    $request 										= $astDB->escape($_REQUEST['request']);
	//$dispo_stats 									= $astDB->escape($_REQUEST['statuses']);
	
    if (empty($fromDate)) {
    	$fromDate 									= date("Y-m-d")." 00:00:00";
    }
    
    if (empty($toDate)) {
    	$toDate 									= date("Y-m-d")." 23:59:59";
    }
		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
		//die($fromDate." - ".$toDate);									=> $err_msg
	} else {            
		// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
		// every time we need to filter out requests
		$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
		
		if ($tenant) {
			$astDB->where("user_group", $log_group);
		} else {
			if (strtoupper($log_group) != 'ADMIN') {
				if ($user_level < 9) {
					$astDB->where("user_group", $log_group);
				}
			}
		}
			
		// check if MariaDB slave server available
		$rslt										= $goDB
			->where('setting', 'slave_db_ip')
			->where('context', 'creamy')
			->getOne('settings', 'value');
		$slaveDBip 									= $rslt['value'];
		
		if (!empty($slaveDBip)) {
			$astDB 									= new MySQLiDB($slaveDBip, $VARDB_user, $VARDB_pass, $VARDB_database);

			if (!$astDB) {
				echo "Error: Unable to connect to MariaDB slave server." . PHP_EOL;
				echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
				exit;
				//die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
			}			
		}
		
		// SALES TRACKER
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

            if (!empty($sstatuses))
                    $sstatuses = implode("','",$sstatuses);

            $Qstatus2 = $astDB
                    ->where("sale", "Y")
                    ->where("campaign_id", $campaignID)
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

            if (!empty($cstatuses)) {
                    $cstatuses = implode("','",$cstatuses);
            }

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
	
			if (strtolower($request) === 'outbound') {
				$outbound_query 				= "
					SELECT distinct(vl.phone_number) as phone_number, 
						vl.lead_id as lead_id, 
						vlo.call_date as call_date,
						us.full_name as agent, 
						vl.first_name as first_name,
						vl.last_name as last_name,
						vl.address1 as address,
						vl.city as city,
						vl.state as state, 
						vl.postal_code as postal,
						vl.email as email,
						vl.alt_phone as alt_phone,
						vl.comments as comments,vl.lead_id 
					FROM vicidial_log as vlo, vicidial_list as vl, vicidial_users as us 
					WHERE us.user = vlo.user AND vl.phone_number = vlo.phone_number 
					AND vl.lead_id = vlo.lead_id 
					#AND vlo.length_in_sec > '0'
					AND vlo.status in ('$statuses') AND date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					AND vlo.campaign_id IN ($imploded_camp) $ul 
					order by vlo.call_date ASC 
					limit 2000
				";
				
				$outbound_sql 	= $astDB->rawQuery($outbound_query);
				$outbound_result	= "";
				$sale_num_value		= 1;
				
				foreach ($outbound_sql as $row) {
					$sale_num[] 				= $sale_num_value;
					$outbound_result 			= $row['phone_number'];
					$call_date[] 				= $row['call_date'];
					$agent[] 				= $row['agent'];
					$lead_id[] 				= $row['lead_id'];
					$phone_number[] 			= $row['phone_number'];
					$first_name[] 				= $row['first_name'];
					$last_name[] 				= $row['last_name'];
					$address[] 				= $row['address'];
					$city[] 				= $row['city'];
					$state[] 				= $row['state'];
					$postal[] 				= $row['postal'];
					$email[] 				= $row['email'];
					$alt_phone[] 				= $row['alt_phone'];
					$comments[] 				= $row['comments'];
					$sale_num_value++;
				}
			}
		
			if (strtolower($request) === 'inbound') {
				$inbound_query = "
                	                SELECT closer_campaigns FROM vicidial_campaigns
                        	        WHERE campaign_id IN ($imploded_camp)
                                	ORDER BY campaign_id
        	                ";
	                        $closer_query = $astDB->rawQuery($inbound_query);
				
				foreach($closer_query as $data){
                	                if(!empty($data['closer_campaigns'])){
        	                        $trimmed_cc = rtrim($data['closer_campaigns'], " - ");
	                                $closer_camp[] = $trimmed_cc;
                                	}//not null
                        	}
				//iterate thru array closer_camp to separate merged closer campaignsi
	                        $imploded = implode(" ", $closer_camp);
        	                $exploded = explode(" ", $imploded);
				$campaign_inb_query = "vlo.campaign_id IN ('".implode("','",$exploded)."')";
				
				$inbound_query 	= "
					SELECT distinct(vl.phone_number) as phone_number, 
						vl.lead_id as lead_id, 
						vlo.call_date as call_date,
						us.full_name as agent, 	
						vl.first_name as first_name,
						vl.last_name as last_name,
						vl.address1 as address,
						vl.city as city,
						vl.state as state, 
						vl.postal_code as postal,
						vl.email as email,
						vl.alt_phone as alt_phone,
						vl.comments as comments,
						vl.lead_id FROM vicidial_closer_log as vlo, 
						vicidial_list as vl, 
						vicidial_users as us 
					WHERE us.user = vl.user 
					AND vl.phone_number = vlo.phone_number 
					AND vl.lead_id=vlo.lead_id 
					#AND vlo.length_in_sec > '0' 
					AND date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					AND $campaign_inb_query AND vlo.status in ('$statuses') $ul 
					order by vlo.call_date ASC 
					limit 2000
				";
				
				$inbound_sql = $astDB->rawQuery($inbound_query);
				$inbound_result = "";
				$sale_num_value = 1;
				
				foreach ($inbound_sql as $row) {
					$sale_num[] 				= $sale_num_value;
					$inbound_result 			= $row['phone_number'];
					$call_date[] 				= $row['call_date'];
					$agent[] 				= $row['agent'];
					$lead_id[]				= $row['lead_id'];
					$phone_number[] 			= $row['phone_number'];
					$first_name[] 				= $row['first_name'];
					$last_name[] 				= $row['last_name'];
					$address[] 				= $row['address'];
					$city[] 				= $row['city'];
					$state[] 				= $row['state'];
					$postal[] 				= $row['postal'];
					$email[] 				= $row['email'];
					$alt_phone[] 				= $row['alt_phone'];
					$comments[] 				= $row['comments'];
					$sale_num_value++;
				}
			}
		
			$apiresults = array(
				"result" => "success",
				"outbound_result" 				=> $outbound_result, 
				"inbound_result" 				=> $inbound_result, 
				"sale_num" 					=> $sale_num, 
				"call_date" 					=> $call_date, 
				"agent" 					=> $agent, 
				"phone_number" 					=> $phone_number, 
				"lead_id" 					=> $lead_id, 
				"first_name" 					=> $first_name, 
				"last_name" 					=> $last_name,
				"address" 					=> $address, 
				"city" 						=> $city, 
				"state" 					=> $state, 
				"postal" 					=> $postal, 
				"email" 					=> $email, 
				"alt_phone" 					=> $alt_phone, 
				"comments" 					=> $comments,
			);
			
		return $apiresults;
	}

?>
