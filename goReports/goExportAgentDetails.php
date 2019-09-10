<?php
/**
 * @file        goExportAgentDetails.php
 * @brief       API to for Reports (Agent Details)
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      AlexANDer Jim Abenoja
 * @author		Demian LizANDro A. Biscocho
 * @author		Thom Bernarth Patacsil
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
	include_once("goReportsFunctions.php");
	
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);

	// need function go_sec_convert();
    $pageTitle 										= strtolower($astDB->escape($_REQUEST['pageTitle']));
    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaignID 									= $astDB->escape($_REQUEST['campaignID']);
    $request 										= $astDB->escape($_REQUEST['request']);
    $dispo_stats 									= $astDB->escape($_REQUEST['statuses']);
	
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
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
		//die($fromDate." - ".$toDate);
	} elseif ($pageTitle == "sales_tracker" && empty($request)) {
		$err_msg 									= error_handle("40001");
		$apiresults 								= array(
			"code" 										=> "40001", 
			"result" 									=> $err_msg
		);
	} elseif ($pageTitle == "sales_agent" && empty($request)) {
		$err_msg 									= error_handle("40001");
		$apiresults 								= array(
			"code" 										=> "40001", 
			"result" 									=> $err_msg
		);
	} elseif (!in_array($pageTitle, $defPage)) {
	 	$err_msg 									= error_handle("10004");
		$apiresults 								= array(
			"code" 										=> "10004", 
			"result" 									=> $err_msg
		);
	} else {
		$goReportsReturn 							= go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $log_user, $log_group,$astDB, $dispo_stats, $goDB);
		$apiresults 								= array(
			"result" 									=> "success", 
			"getReports" 								=> $goReportsReturn
		);
	}
	return $apiresults;	
	
	function go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $log_user, $log_group, $astDB, $dispo_stats, $goDB) {
		if (!empty($campaignID) || $pageTitle == 'call_export_report') {
			$date1 										= new DateTime($fromDate);
			$date2 										= new DateTime($toDate);
			$interval 									= date_diff($date1,$date2);
			$date_diff 									= $interval->format('%d');
            		$date_array 								= implode("','",go_get_dates($fromDate, $toDate));
			$file_download 								= 1;            
            		$tenant 									= 0;
            
            // set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
            // every time we need to filter out requests
			if (checkIfTenant ($log_group, $goDB)) {
				$tenant									= 1;
			}
			
            if ($tenant) {
				$astDB->where("user_group", $log_group);
            } else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($user_level > 8) {
						$astDB->where("user_group", $log_group);
					}
				}
            }	
            
            //Initialise Values                        
			if ($pageTitle != 'inbound_report') {
				$resultu 								= $astDB
					->where("campaign_id", $campaignID)
					->getValue("vicidial_campaigns", "campaign_name");
			} else {
				$resultu 								= $astDB
					->where("uniqueid_status_prefix", $log_group)
					->getValue("vicidial_inbound_groups", "group_name");
			}
            
			if ($astDB->count < 1) {
				$err_msg 								= error_handle("41004", "campaignID. Doesn't exist");
				$apiresults 							= array(
					"code" 									=> "41006", 
					"result" 								=> $err_msg
				); 
			} else {
				//foreach ($resultu as $campaign_name) {
					//$return['campaign_name'] 			= $resultu['campaign_name'];
				$return['campaign_name'] 				= $resultu;				
				//}
			}
				
			if (!isset($request) || $request=='') {
				$return['request'] 						= 'daily';
			} else {
				$return['request'] 						= $request;
			}
			
			$Qstatus									= $astDB
				->where("sale", "Y")
				->getOne("vicidial_statuses", "status");
				
			$sstatusRX 									= "";
			$sstatuses 									= array();			
			$a 											= 0;
			
			if ($Qstatus) {
				//foreach ($query as $Qstatus) {
				$goTempStatVal 							= $Qstatus['status'];
				$sstatuses[$a] 							= $Qstatus['status'];
				$sstatusRX							.= "{$goTempStatVal}|";
					//$a++;
				//}			
			}
			
			if (!empty($sstatuses)) {
				$sstatuses 									= implode("','",$sstatuses);
			}

			$Qstatus									= $astDB
				->where("sale", "Y")
				->where("campaign_id", $campaignID)
				->getOne("vicidial_campaign_statuses", "status");
			
			$cstatusRX 									= "";
			$cstatuses 									= array();			
			$b 										= 0;
			
			if ($Qstatus) {
				//foreach ($query as $Qstatus) {
				$goTempStatVal 							= $Qstatus['status'];
				$cstatuses[$b] 							= $Qstatus['status'];
				$cstatusRX							.= "{$goTempStatVal}|";
					//$b++;
				//}			
			}
			
			if (!empty($cstatuses)) {
				$cstatuses 								= implode("','",$cstatuses);
			}
			
			
			if (count($sstatuses) > 0 && count($cstatuses) > 0) {
				$statuses 								= "{$sstatuses}','{$cstatuses}";
				$statusRX 								= "{$sstatusRX}{$cstatusRX}";
			} else {
				$statuses 								= (count($sstatuses) > 0 && count($cstatuses) < 1) ? $sstatuses : $cstatuses;
				$statusRX 								= (count($sstatusRX) > 0 && count($cstatusRX) < 1) ? $sstatusRX : $cstatusRX;
			}
			
			$statusRX 									= trim($statusRX, "|");
			//End initialize		
			//Start Report		
			
			// Agent Time Detail
			if ($pageTitle == "agent_detail") {
				if ($log_group !== "ADMIN") {
					$ul 								= "AND user_group = '$log_group'";
				} else {
					$ul 								= "";
				}

				// BEGIN gather user IDs AND names for matching up later
				/*$query 									= "
					SELECT full_name,user FROM vicidial_users 
					ORDER BY user 
					LIMIT 100000
				";
				
				$user_ct 								= $astDB->rawQuery($query);*/
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}				
				
				$quserct 								= $astDB
					->orderBy("user")
					->get("vicidial_users", 1000, array("full_name" ,"user"));
					
				$user_ct								= $astDB->getRowCount();
					
				if (count($quserct) > 0) {
					foreach ($quserct as $row) {
						$ULname[] 						= $row['full_name'];
						$ULuser[] 						= $row['user'];					
					}
				}
								
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$ULname[] 							= $row['full_name'];
					$ULuser[] 							= $row['user'];
				}*/
				
				// END gather user IDs AND names for matching up later
			
				// BEGIN gather timeclock records per agent
				/*$query 									= "
					SELECT user,SUM(login_sec) AS login_sec FROM vicidial_timeclock_log 
					WHERE event IN('LOGIN','START') AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					GROUP BY user 
					LIMIT 10000000
				";
				
				$timeclock_ct 							= $astDB->rawQuery($query);*/
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
				
				$timeclock_ct 							= $astDB
					->where("event", array("LOGIN", "START"), "IN")
					->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->get("vicidial_timeclock_log", "user, SUM(login_sec) as login_sec");
				
				if ($astDB->count > 0) {
					foreach ($timeclock_ct as $row) {
						$TCuser[] 						= $row['user'];
						$TCtime[] 						= $row['login_sec'];					
					}
				}
				
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$TCuser[] 							= $row['user'];
					$TCtime[] 							= $row['login_sec'];
				}*/
				
				// END gather timeclock records per agent				
				// BEGIN gather pause code information by user IDs
				$sub_statuses 							= '-';
				$sub_statusesTXT 						= '';
				$sub_statusesHEAD 						= '';
				$sub_statusesHTML 						= '';
				$sub_statusesFILE 						= '';
				$sub_statusesTOP 						= array();
				$sub_statusesARY 						= $MT;
				
				$PCusers 							= '-';
				$PCusersARY 							= $MT;
				$PCuser_namesARY 						= $MT;

				$i								= 0;
				$a								= 1;
				$sub_status_count						= 0;
				$user_count							= 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
			
				$pause_sec_ct 							= $astDB
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("pause_sec", 0, ">")
					->where("pause_sec", 65000, "<")
					->where("campaign_id", $campaignID)
					->groupBy("user, sub_status")
					->orderBy("user", "DESC", array("sub_status"))
					->get("vicidial_agent_log", 1000, "user, SUM(pause_sec) as pause_sec, sub_status");
		
				if ($astDB->count > 0) {
					foreach ($pause_sec_ct as $row) {
						$PCuser[] 						= $row['user'];
						$PCpause_sec[] 					= $row['pause_sec'];
						$sub_status[] 					= $row['sub_status'];
						
						if (!preg_match("/-$sub_status-/", $sub_statuses)) {
							$sub_statusesFILE 			.= ",$sub_status";
							$sub_statuses 				.= "$sub_status-";
							$sub_statusesARY[$sub_status_count] = $sub_status;
							$sub_statusesTOP[] 			= $sub_status;
							//$sub_status_count++;
						}
						
						if (!preg_match("/-$PCuser-/", $PCusers)) {
							$PCusersARY[$user_count] 	= $PCuser;
							//$user_count++;
						}						
					}
				}
				
				$j										= 0;
				$k										= 0;
				$uc										= 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}

				if ("ALL" === strtoupper($campaignID)) {
					$SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");

					foreach($SELECTQuery as $camp_val){
						$array_camp[] = $camp_val["campaign_id"];
					}
				}else{
						$array_camp[] = $campaignID;
				}
				
				/*
				$cols									= array(
					"user",
					"wait_sec",
					"talk_sec",
					"dispo_sec",
					"pause_sec",
					"lead_id",
					"status",
					"dead_sec"
				);
				
				$agent_time_ct 							= $astDB
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("campaign_id", $campaignID)
					->get("vicidial_agent_log", 1000, $cols);
				*/
				
				$cols = array(
					"vu.full_name",
					"val.user",
					"sum(wait_sec) as wait_sec",
					"sum(talk_sec) as talk_sec",
					"sum(dispo_sec) as dispo_sec",
					"sum(pause_sec) as pause_sec",
					"count(lead_id) as calls",
					"lead_id",
					"status",
					"sum(dead_sec) as dead_sec",
					"(sum(talk_sec) - sum(dead_sec)) as customer"
				);
				
				$agent_time_ct = $astDB
					->join("vicidial_users vu", "val.user = vu.user", "LEFT")
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("campaign_id", $array_camp, "IN")
					->groupBy("val.user")
					->get("vicidial_agent_log val", $limit, $cols);

				if ($astDB->count >0) {
					foreach ($agent_time_ct as $row) {
						$user 							= $row['user'];
						$wait 							= $row['wait_sec'];
						$talk 							= $row['talk_sec'];
						$dispo 							= $row['dispo_sec'];
						$pause 							= $row['pause_sec'];
						$lead 							= $row['lead_id'];
						$status 						= $row['status'];
						$dead 							= $row['dead_sec'];
						$customer 						= $row['customer'];
						$calls							= $row['calls'];
						
						/*if ($wait > 65000) { $wait  	= 0; }
						if ($talk > 65000) { $talk		= 0; }
						if ($dispo > 65000) { $dispo	= 0; }
						if ($pause > 65000) { $pause	= 0; }
						if ($dead > 65000) { $dead		= 0; }
						if ($customer < 1) { $customer	= 0; }*/
						
						$TOTwait 						= ($TOTwait + $wait);
						$TOTtalk 						= ($TOTtalk + $talk);
						$TOTdispo 						= ($TOTdispo + $dispo);
						$TOTpause 						= ($TOTpause + $pause);
						$TOTdead 						= ($TOTdead + $dead);
						$TOTcustomer 					= ($TOTcustomer + $customer);
						$TOTALtime 						= ($TOTALtime + $pause + $dispo + $talk + $wait);
						
						if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
							$TOTcalls++;
						}
						
						$user_found						= 0;
						
						if ($uc < 1) {
							$Suser[$uc] 				= $user;
							$uc++;
						}
						
						$m								= 0;
						
						while ( ($m < $uc) AND ($m < 50000) ) {
							if ($user == $Suser[$m]) {
								$user_found++;
								$Swait[$m] 				= ($Swait[$m] + $wait);
								$Stalk[$m] 				= ($Stalk[$m] + $talk);
								$Sdispo[$m] 			= ($Sdispo[$m] + $dispo);
								$Spause[$m] 			= ($Spause[$m] + $pause);
								$Sdead[$m] 				= ($Sdead[$m] + $dead);
								$Scustomer[$m] 			= ($Scustomer[$m] + $customer);
								
								//if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
								//	$Scalls[$m]++;
									$Scalls[$m]			= ($Scalls[$m] + $calls);
								//}
							}
							
							$m++;
						}
						
						if ($user_found < 1) {
							//$Scalls[$uc] 				= 0;
							$Suser[$uc] 				= $user;
							$Swait[$uc] 				= $wait;
							$Stalk[$uc] 				= $talk;
							$Sdispo[$uc] 				= $dispo;
							$Spause[$uc] 				= $pause;
							$Sdead[$uc] 				= $dead;
							$Scustomer[$uc] 			= $customer;
							
							//if ($lead > 0) {
								//$Scalls[$uc]++;
								$Scalls[$uc]                     = $calls;
							//}
							
							$uc++;
						}						
					}
				}
				//# END Gather all agent time records AND parse through them in PHP to save on DB load
			
				//////////////////////////////////////
				//# END gathering information FROM the database section
				//////////////////////////////////////
			
				//# BEGIN print the output to screen or put into file output variable
				/*
				if ($file_download > 0)
					{
					$file_output  = "CAMPAIGN,$campaignID - ".$resultu['campaign_name']."\n";
					$file_output .= "DATE RANGE,$fromDate TO $toDate\n\n";
					$file_output .= "USER,ID,CALLS,TIME CLOCK,AGENT TIME,WAIT,TALK,DISPO,PAUSE,WRAPUP,CUSTOMER,$sub_statusesFILE\n";
					}
				*/
				//# END print the output to screen or put into file output variable
			
				//////////////////////////////////////
				//# BEGIN formatting data for output section
				//////////////////////////////////////
			
				//# BEGIN loop through each user formatting data for output
				$AUTOLOGOUTflag					= 0;
				$m								= 0;
				$rowId							= 1;
				
				while ( ($m < $uc) AND ($m < 50000) ) {
					$SstatusesHTML						= "";
					$SstatusesFILE						= "";
					$Stime[$m] 							= ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
					$RAWuser 							= $Suser[$m];
					$RAWcalls 							= $Scalls[$m];
					$RAWtimeSEC 						= $Stime[$m];
			
					$Swait[$m] 						= convert($Swait[$m]); 
					$Stalk[$m] 						= convert($Stalk[$m]); 
					$Sdispo[$m] 					= convert($Sdispo[$m]); 
					$Spause[$m] 					= convert($Spause[$m]); 
					$Sdead[$m] 						= convert($Sdead[$m]); 
					$Scustomer[$m] 					= convert($Scustomer[$m]); 
					$Stime[$m] 						= convert($Stime[$m]); 
			
					$RAWtime 						= $Stime[$m];
					$RAWwait 						= $Swait[$m];
					$RAWtalk 						= $Stalk[$m];
					$RAWdispo						= $Sdispo[$m];
					$RAWpause						= $Spause[$m];
					$RAWdead 						= $Sdead[$m];
					$RAWcustomer 						= $Scustomer[$m];
			
					$n							= 0;
					$user_name_found					= 0;
					
					while ($n < $user_ct) {
						if ($Suser[$m] == $ULuser[$n]) {
							$user_name_found++;
							$RAWname 					= $ULname[$n];
							$Sname[$m] 					= $ULname[$n];
						}
						
						$n++;
					}
					
					if ($user_name_found < 1) {
						$RAWname 						= "NOT IN SYSTEM";
						$Sname[$m] 						= $RAWname;
					}
			
					$n 							= 0;
					$punches_found						= 0;
					
					while ($n < $punches_to_print) {
						if ($Suser[$m] == $TCuser[$n]) {
							$punches_found++;
							$RAWtimeTCsec					= $TCtime[$n];
							$TOTtimeTC 					= ($TOTtimeTC + $TCtime[$n]);
							$StimeTC[$m] 					= convert($TCtime[$n]); 
							$RAWtimeTC 					= $StimeTC[$m];
							$StimeTC[$m] 					= sprintf("%10s", $StimeTC[$m]);
						}
						
						$n++;
					}
					
					if ($punches_found < 1) {
						$RAWtimeTCsec 					= "0";
						$StimeTC[$m] 					= "0:00"; 
						$RAWtimeTC 					= $StimeTC[$m];
						$StimeTC[$m] 					= sprintf("%10s", $StimeTC[$m]);
					}
			
					// Check if the user had an AUTOLOGOUT timeclock event during the time period
					$TCuserAUTOLOGOUT 					= ' ';
					/*$query 								= "
						SELECT COUNT(*) as cnt FROM vicidial_timeclock_log 
						WHERE event='AUTOLOGOUT' AND user = '$Suser[$m]' 
						AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
					";
					
					$timeclock_ct 						= $astDB->rawQuery($query);*/
									
					$timeclock_ct						= $astDB						
						->where("event", "AUTOLOGOUT")
						->where("user", $user[$m])
						->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
						->getValue("vicidial_timeclock_log", "count(*)");
						
					if ($timeclock_ct > 0) {
						$TCuserAUTOLOGOUT 				= '*';
						$AUTOLOGOUTflag++;
					}
			
					// BEGIN loop through each status //
					$n							= 0;
					
					while ($n < $sub_status_count) {
						$Sstatus						= $sub_statusesARY[$n];
						$SstatusTXT						= "";
						
						// BEGIN loop through each stat line //
						$i						= 0;
						$status_found					= 0;
						
						while ( ($i < $pause_sec_ct) AND ($status_found < 1) ) {
							if ( ($Suser[$m] == $PCuser[$i]) AND ($Sstatus == $sub_status[$i]) ) {
								$USERcodePAUSE_MS 		= convert($PCpause_sec[$i]);
								
								if (strlen($USERcodePAUSE_MS)<1) {
									$USERcodePAUSE_MS	= '0';
								}
								
								$pfUSERcodePAUSE_MS 		= sprintf("%10s", $USERcodePAUSE_MS);
	
								$SstatusesFILE 			.= ",$USERcodePAUSE_MS";
								//$sub_statusesTOP[$m]
								$Sstatuses[$m] 			.= "$USERcodePAUSE_MS";
								$status_found++;
							}
								
							$i++;
						}
						
						if ($status_found < 1) {
							$SstatusesFILE 				.= ",0:00";
							//$Sstatuses[$m] .= " 0:00";
						}
						// END loop through each stat line //
						
						$n++;
						
						if (!empty($Sstatuses[$m])) {
							$Sstatuses[$m] 				.= ",";
						}
					}

					// END loop through each status //					
					//if (is_null($Scalls[$m])) {
					//	$Scalls[$m] 					= 0;
					//}

					$Toutput 						= array(
						"name" 							=> $Sname[$m], 
						"user" 							=> $Suser[$m], 
						"number_of_calls" 					=> $Scalls[$m], 
						"agent_time" 						=> $Stime[$m], 
						"wait_time" 						=> $Swait[$m], 
						"talk_time" 						=> $Stalk[$m], 
						"dispo_time" 						=> $Sdispo[$m], 
						"pause_time" 						=> $Spause[$m], 
						"wrap_up" 						=> $Sdead[$m], 
						"customer_time" 					=> $Scustomer[$m]
					);
			
					$Sstatuses[$m] 						= rtrim( $Sstatuses[$m], ",");
					
					$Boutput 						= array(
						"rowID" 						=> $rowId, 
						"name" 							=> $Sname[$m], 
						"statuses" 						=> $Sstatuses[$m]
					);
					
					$BoutputFile 						= array(
						"statuses" 						=> $Sstatuses[$m]
					);

					$TOPsorted_output[$m] 				= $Toutput;
					$BOTsorted_output[$m] 				= $Boutput;
					$TOPsorted_outputFILE[$m] 			= array_merge($Toutput, $BoutputFile);
			
					if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
						if ($file_download > 0) {
							$file_output 				.= "$fileToutput";
						}
					}
					
					if ($TOPsortMAX < $TOPsortTALLY[$m]) {
						$TOPsortMAX 					= $TOPsortTALLY[$m];
					}
			
					$m++;
					$rowId++;
				}
					//# END loop through each user formatting data for output
								
				$TOT_AGENTS 							= 'AGENTS: '.$m;
				// 	// BEGIN sort through output to display properly //
				if ( ($TOT_AGENTS > 0) AND (preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) ) {
					if (preg_match("/ID/",$stage)) {
						sort($TOPsort, SORT_NUMERIC);
					}
					
					if (preg_match("/TIME|LEADS|TCLOCK/",$stage)) {
						rsort($TOPsort, SORT_NUMERIC);
					}
					
					if (preg_match("/NAME/",$stage)) {
						rsort($TOPsort, SORT_STRING);
					}
			
					$m							= 0;
					
					while ($m < $k) {
						$sort_split 					= explode("-----",$TOPsort[$m]);
						$i 								= $sort_split[1];
						$sort_order[$m] 				= "$i";
						//if ($file_download > 0)
						//	{$file_output .= "$TOPsorted_outputFILE[$i]";}
						$m++;
					}
				}
				// END sort through output to display properly //
			
				//////////////////////////////////////
				//# END formatting data for output section
				//////////////////////////////////////
			
				//////////////////////////////////////
				//# BEGIN last line totals output section
				//////////////////////////////////////
				$SUMstatusesHTML						= "";
				//$SUMstatusesFILE						= "";
				$TOTtotPAUSE 							= 0;
				$n										= 0;
			
				while ($n < $sub_status_count) {
					$Scalls									= 0;
					$Sstatus=$sub_statusesARY[$n];
					$SUMstatusTXT							= "";
					// BEGIN loop through each stat line //
					$i										= 0; 
					$status_found							= 0;
					
					while ($i < $pause_sec_ct) {
						if ($Sstatus == "$sub_status[$i]") {
							$Scalls 					= ($Scalls + $PCpause_sec[$i]);
							$status_found++;
						}
						
						$i++;
					}
					// END loop through each stat line //
					if ($status_found < 1) {
						$SUMstatuses[$n] 				= "00:00:00";
					} else {
						$TOTtotPAUSE 					= ($TOTtotPAUSE + $Scalls);
			
						//$USERsumstatPAUSE_MS 			= gmdate('H:i:s', $Scalls);
						$USERsumstatPAUSE_MS			= convert($Scalls); 
						$pfUSERsumstatPAUSE_MS 			= sprintf("%11s", $USERsumstatPAUSE_MS);
	
						//$SUMstatusesFILE .= ",$USERsumstatPAUSE_MS";
						$SUMstatuses[$n] 				= $USERsumstatPAUSE_MS;
					}
					
					$n++;
				}
				// END loop through each status //
		
				// call function to calculate AND print dialable leads

				$TOTwait                                                                = convert($TOTwait);
				$TOTtalk                                                                = convert($TOTtalk);
				$TOTdispo                                                               = convert($TOTdispo);
				$TOTpause                                                               = convert($TOTpause);
				$TOTdead                                                                = convert($TOTdead);
				$TOTcustomer                                                    		= convert($TOTcustomer);
				$TOTALtime                                                              = convert($TOTALtime);
				$TOTtimeTC                                                              = convert($TOTtimeTC);
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"TOPsorted_output" 						=> $TOPsorted_output, 
					"sub_statusesTOP" 						=> $sub_statusesTOP, 
					"BOTsorted_output" 						=> $BOTsorted_output, 
					"SUMstatuses" 							=> $SUMstatuses, 
					"TOTwait" 								=> $TOTwait, 
					"TOTtalk" 								=> $TOTtalk, 
					"TOTdispo" 								=> $TOTdispo, 
					"TOTpause" 								=> $TOTpause, 
					"TOTdead" 								=> $TOTdead, 
					"TOTcustomer" 							=> $TOTcustomer, 
					"TOTALtime" 							=> $TOTALtime, 
					"TOTtimeTC" 							=> $TOTtimeTC, 
					"TOT_AGENTS" 							=> $TOT_AGENTS, 
					"TOTcalls" 								=> $TOTcalls, 
					"FileExport" 							=> $TOPsorted_outputFILE,
					"query" => $astDB->getLastQuery()
				);
				
				return $apiresults;				
			}	
			return $apiresults;
		}
	}
	// End of Function go_get_reports

?>
