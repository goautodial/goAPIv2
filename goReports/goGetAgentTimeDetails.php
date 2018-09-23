<?php
/**
 * @file        goGetAgentTimeDetails.php
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
	
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']));

    $pageTitle 											= strtolower($astDB->escape($_REQUEST['pageTitle']));
    $fromDate 											= (empty($_REQUEST['fromDate']) ? date("Y-m-d")." 00:00:00" : $astDB->escape($_REQUEST['fromDate']));
    $toDate 											= (empty($_REQUEST['toDate']) ? date("Y-m-d")." 23:59:59" : $astDB->escape($_REQUEST['toDate']));
    $campaign_id 										= $astDB->escape($_REQUEST['campaignID']);
    $request 											= $astDB->escape($_REQUEST['request']);
	$limit												= 1000;
	$defPage 											= "agent_detail";

    // Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
	} else {            
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {				
			// Agent Time Detail
			if ($pageTitle == "agent_detail") {			
				// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
				// every time we need to filter out requests
				$tenant									= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($userlevel > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
				
				$TOTtimeTC								= array();
				
				$timeclock_ct 							= $astDB
					->where("event", array("LOGIN", "START"), "IN")
					->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->groupBy("user")
					->get("vicidial_timeclock_log", "user, SUM(login_sec) as login_sec");
				
				if ($astDB->count > 0) {
					foreach ($timeclock_ct as $row) {
						$TCuser 						= $row['user'];
						$TCtime 						= $row['login_sec'];
						
						array_push($TOTtimeTC, $TCtime);
					}
				}
				
				$sub_statuses 							= '-';
				$sub_statusesTXT 						= '';
				$sub_statusesHEAD 						= '';
				$sub_statusesHTML 						= '';
				$sub_statusesFILE 						= '';
				$sub_statusesTOP 						= array();
				$sub_statusesARY 						= array();
				
				$PCusers 								= '-';
				$PCuser_namesARY						= array();
				$PCusersARY 							= array();
				$PCpause_secsARY						= array();
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
			
				$cols									= array(
					"vu.full_name",
					"val.user",
					"SUM(pause_sec) as pause_sec",
					"sub_status"
				);
				
				$pcs_data 								= $astDB
					->join("vicidial_users as vu", "val.user = vu.user", "LEFT")
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("pause_sec", 0, ">")
					->where("pause_sec", 65000, "<")
					->where("campaign_id", $campaign_id)
					->where("sub_status", array("LAGGED", "LOGIN"), "NOT IN")
					->groupBy("vu.user,sub_status")
					->orderBy("vu.user,sub_status")
					->get("vicidial_agent_log as val", $limit, $cols);
		
				if ($astDB->count > 0) {
					foreach ($pcs_data as $pc_data) {					
						$PCfull_name[]					= $pc_data['full_name'];
						$PCuser[] 						= $pc_data['user'];
						$PCpause_sec[] 					= $pc_data['pause_sec'];
						$sub_status[] 					= $pc_data['sub_status'];				
						
					}
					
					$Boutput 							= array(
						"full_name" 						=> $PCfull_name, 
						"user" 								=> $PCuser, 
						"pause_sec" 						=> $PCpause_sec,
						"sub_status"						=> $sub_status
					);
					
					$SUMstatuses						= array_sum($PCpause_secsARY);
					
					$BoutputFile 						= array(
						"statuses" 							=> $PCpause_secsARY
					);				
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
				
				$cols									= array(
					"vu.full_name",
					"val.user",
					"sum(wait_sec) as wait_sec",
					"sum(talk_sec) as talk_sec",
					"sum(dispo_sec) as dispo_sec",
					"sum(pause_sec) as pause_sec",
					"count(lead_id) as calls",
					"status",
					"sum(dead_sec) as dead_sec",
					"(sum(talk_sec) - sum(dead_sec)) as customer"
				);
				
				$agenttd	 							= $astDB
					->join("vicidial_users vu", "val.user = vu.user", "LEFT")
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("campaign_id", $campaign_id)
					->groupBy("val.user")
					->get("vicidial_agent_log val", $limit, $cols);
					
				$usercount								= $astDB->getRowCount();
					
				if ($astDB->count >0) {	
					$TOTwait 							= array();
					$TOTtalk 							= array();
					$TOTdispo 							= array();
					$TOTpause 							= array();
					$TOTdead 							= array();
					$TOTcustomer 						= array();
					$TOTALtime 							= array();
					$TOT_AGENTS							= $usercount;
					$TOTcalls							= array();
					
					$nameARY							= array();
					$userARY							= array();
					$wait_secARY						= array();
					$talk_secARY						= array();
					$dispo_secARY						= array();
					$pause_secARY						= array();
					$dead_secARY						= array();
					$customerARY						= array();
					$agent_timeARY						= array();
					$callsARY							= array();
					
					foreach ($agenttd as $row) {
						$name							= $row['full_name'];
						$user							= $row['user'];
						$wait							= $row['wait_sec'];
						$talk							= $row['talk_sec'];
						$dispo							= $row['dispo_sec'];
						$pause							= $row['pause_sec'];
						$calls	 						= $row['calls'];
						$status 						= $row['status'];
						$dead_sec						= $row['dead_sec'];
						$customer						= $row['customer'];
						$time							= ($wait + $talk + $dispo + $pause);
						$time							= $time;
						
						if ($wait > 65000) { $wait  	= 0; }
						if ($talk > 65000) { $talk		= 0; }
						if ($dispo > 65000) { $dispo	= 0; }
						if ($pause > 65000) { $pause	= 0; }
						
						if ($dead_sec > 65000) { 
							$dead_sec					= 0; 
						}
											
						if ($customer < 1) {
							$customer					= 0;
						}
						
						array_push($nameARY, $name);
						array_push($userARY, $user);
						array_push($wait_secARY, $wait);
						array_push($talk_secARY, $talk);
						array_push($dispo_secARY, $dispo);
						array_push($pause_secARY, $pause);
						array_push($dead_secARY, $dead_sec);
						array_push($customerARY, $customer);
						array_push($agent_timeARY, $time);
						array_push($callsARY, $calls);
						
						array_push($TOTwait, $wait);
						array_push($TOTtalk, $talk);
						array_push($TOTdispo, $dispo);
						array_push($TOTpause, $pause);
						array_push($TOTdead, $dead_sec);
						array_push($TOTcustomer, $customer);
						array_push($TOTALtime, $talk);
						array_push($TOTcalls, $calls);
					}
					
					$TOTwait 							= gmdate('H:i:s', array_sum($TOTwait));
					$TOTtalk 							= gmdate('H:i:s', array_sum($TOTtalk));
					$TOTdispo 							= gmdate('H:i:s', array_sum($TOTdispo));
					$TOTpause 							= gmdate('H:i:s', array_sum($TOTpause));
					$TOTdead 							= gmdate('H:i:s', array_sum($TOTdead));
					$TOTcustomer 						= gmdate('H:i:s', array_sum($TOTcustomer));
					$TOTALtime 							= gmdate('H:i:s', array_sum($TOTALtime));
					$TOTtimeTC 							= gmdate('H:i:s', array_sum($TOTtimeTC));
					$TOT_AGENTS 						= 'AGENTS: '.$usercount;
					$TOTcalls							= array_sum($TOTcalls);
				}
						
				// Check if the user had an AUTOLOGOUT timeclock event during the time period
				$TCuserAUTOLOGOUT 						= ' ';
								
				$timeclock_ct							= $astDB						
					->where("event", "AUTOLOGOUT")
					->where("user", $user)
					->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->getValue("vicidial_timeclock_log", "count(*)");
					
				if ($timeclock_ct > 0) {
					$TCuserAUTOLOGOUT 					= '*';
				}				

				$Toutput 								= array(
					"name" 									=> $nameARY, 
					"user" 									=> $userARY, 
					"number_of_calls" 						=> $callsARY, 
					"agent_time" 							=> $agent_timeARY, 
					"wait_time" 							=> $wait_secARY, 
					"talk_time" 							=> $talk_secARY, 
					"dispo_time" 							=> $dispo_secARY, 
					"pause_time" 							=> $pause_secARY, 
					"wrap_up" 								=> $dead_secARY, 
					"customer_time" 						=> $customerARY
				);						

				$TOPsorted_output 						= $Toutput;
		
				if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
					if ($file_download > 0) {
						$file_output 					.= "$fileToutput";
					}
				}
				
				if ($TOPsortMAX < $TOPsortTALLY) {
					$TOPsortMAX 						= $TOPsortTALLY;
				}

				$apiresults 							= array(
					"result" 								=> "success", 
					"TOPsorted_output" 						=> $TOPsorted_output, 
					"PC_statuses"							=> $pcs_data, //Pause Code data
					"TOTwait" 								=> $TOTwait, 
					"TOTtalk" 								=> $TOTtalk, 
					"TOTdispo" 								=> $TOTdispo, 
					"TOTpause" 								=> $TOTpause, 
					"TOTdead" 								=> $TOTdead, 
					"TOTcustomer" 							=> $TOTcustomer, 
					"TOTALtime" 							=> $TOTALtime, 
					"TOTtimeTC" 							=> $TOTtimeTC, 
					"TOT_AGENTS" 							=> $TOT_AGENTS, 
					"TOTcalls" 								=> $TOTcalls
				);
				
				return $apiresults;				
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
