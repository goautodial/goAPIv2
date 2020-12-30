<?php
/**
 * @file        goGetAgentTimeDetails.php
 * @brief       API for Agent Time Details Reports
 * @copyright   Copyright (c) 2020 GOautodial Inc.
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

	$fromDate 										= (empty($_REQUEST['fromDate']) ? date("Y-m-d")." 00:00:00" : $astDB->escape($_REQUEST['fromDate']));
	$toDate 										= (empty($_REQUEST['toDate']) ? date("Y-m-d")." 23:59:59" : $astDB->escape($_REQUEST['toDate']));
	$campaign_id 									= $astDB->escape($_REQUEST['campaignID']);
	$request 										= $astDB->escape($_REQUEST['request']);
	$limit											= 100;
    
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 								= array(
			"result" 									=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 								= array(
			"result" 									=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} else {            
		// check if goUser and goPass are valid
		$fresults 									= $astDB
			->where("user", $goUser)
			->getOne("vicidial_users", "user,user_level,user_group");
		
		$goapiaccess 								= $astDB->getRowCount();
		$userlevel 									= $fresults["user_level"];
		$usergroup 									= $fresults["user_group"];
		//$apiresults = array("data" => $alex);	

		if ($goapiaccess > 0 && $userlevel > 7) {
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			//$tenant									= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
            $tenant                                 = ($userlevel < 9 && $usergroup !== "ADMIN") ? 1 : 0;
			
			// check if MariaDB slave server available
			$rslt									= $goDB
				->where('setting', 'slave_db_ip')
				->where('context', 'creamy')
				->getOne('settings', 'value');
			$slaveDBip 								= $rslt['value'];
			
			if (!empty($slaveDBip)) {
				$astDB = new MySQLiDB($slaveDBip, $VARDB_user, $VARDB_pass, $VARDB_database);

				if (!$astDB) {
					echo "Error: Unable to connect to MariaDB slave server." . PHP_EOL;
					echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
					exit;
					//die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
				}			
			}
			
			if ($tenant) {
				$astDB->where("user_group", $usergroup);
			} else {
				if (strtoupper($usergroup) != 'ADMIN') {
					if ($userlevel < 9) {
						$astDB->where("user_group", $usergroup);
					}
				}					
			}
			
			if ("ALL" === strtoupper($campaign_id)) {
				$SELECTQuery 						= $astDB->get("vicidial_campaigns", NULL, "campaign_id");

				foreach($SELECTQuery as $camp_val){
					$array_camp[] 					= $camp_val["campaign_id"];
				}
			} else {
				$array_camp[] 						= $campaign_id;
			}
			
			$imploded_camp 							= "'".implode("','", $array_camp)."'";
			/*	
			$TOTtimeTC = array();
				
			$timeclock_ct = $astDB
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
				
			$sub_statuses 		= '-';
			$sub_statusesTXT 	= '';
			$sub_statusesHEAD 	= '';
			$sub_statusesHTML 	= '';
			$sub_statusesFILE 	= '';
			$sub_statusesTOP 	= array();
			$sub_statusesARY 	= array();
				
			$PCusers 		= '-';
			$PCuser_namesARY	= array();
			$PCusersARY 		= array();
			$PCpause_secsARY	= array();
				
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($user_level < 9) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}
			
			$cols = array(
				"vu.full_name",
				"val.user",
				"SUM(pause_sec) as pause_sec",
				"sub_status"
			);
				
			$pcs_data = $astDB
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
					$PCfull_name[]	= $pc_data['full_name'];
					$PCuser[] 	= $pc_data['user'];
					$PCpause_sec[] 	= $pc_data['pause_sec'];
					$sub_status[] 	= $pc_data['sub_status'];
				}
					
				$Boutput = array(
						"full_name" 	=> $PCfull_name, 
						"user" 		=> $PCuser, 
						"pause_sec" 	=> $PCpause_sec,
						"sub_status"	=> $sub_status
					);
					
				$SUMstatuses = array_sum($PCpause_secsARY);
					
				$BoutputFile = array(
					"statuses" => $PCpause_secsARY
				);				
			}
				
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($user_level < 9) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}
			/*	
			$cols = array(
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
				
			$agenttd = $astDB
					->join("vicidial_users vu", "val.user = vu.user", "LEFT")
					->where("date_format(val.event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate,$toDate), "BETWEEN")
					->where("campaign_id", $campaign_id)
					->groupBy("val.user")
					->get("vicidial_agent_log val", $limit, $cols);
			$alex[0] = "<pre>".$astDB->getLastQuery()."</pre>";			
			$usercount = $astDB->getRowCount();
					
			if ($astDB->count >0) {	
				$TOTwait 	= array();
				$TOTtalk 	= array();
				$TOTdispo 	= array();
				$TOTpause 	= array();
				$TOTdead 	= array();
				$TOTcustomer 	= array();
				$TOTALtime 	= array();
				$TOT_AGENTS	= $usercount;
				$TOTcalls	= array();
					
				$nameARY	= array();
				$userARY	= array();
				$wait_secARY	= array();
				$talk_secARY	= array();
				$dispo_secARY	= array();
				$pause_secARY	= array();
				$dead_secARY	= array();
				$customerARY	= array();
				$agent_timeARY	= array();
				$callsARY	= array();
					
				foreach ($agenttd as $row) {
					$name		= $row['full_name'];
					$user		= $row['user'];
					$wait		= $row['wait_sec'];
					$talk		= $row['talk_sec'];
					$dispo		= $row['dispo_sec'];
					$pause		= $row['pause_sec'];
					$calls	 	= $row['calls'];
					$status 	= $row['status'];
					$dead_sec	= $row['dead_sec'];
					$customer	= $row['customer'];
					$time		= ($wait + $talk + $dispo + $pause);
					//$time		= $time;
						
					if ($wait > 65000) { $wait = 0; }
					if ($talk > 65000) { $talk = 0; }
					if ($dispo > 65000) { $dispo = 0; }
					if ($pause > 65000) { $pause = 0; }
					
					if ($dead_sec > 65000) { 
						$dead_sec = 0; 
					}
											
					if ($customer < 1) {
						$customer = 0;
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
					array_push($TOTALtime, $time);
					array_push($TOTcalls, $calls);
				}
					
				$TOTwait 	= gmdate('H:i:s', array_sum($TOTwait));
				$TOTtalk 	= gmdate('H:i:s', array_sum($TOTtalk));
				$TOTdispo 	= gmdate('H:i:s', array_sum($TOTdispo));
				$TOTpause 	= gmdate('H:i:s', array_sum($TOTpause));
				$TOTdead 	= gmdate('H:i:s', array_sum($TOTdead));
				$TOTcustomer 	= gmdate('H:i:s', array_sum($TOTcustomer));
				$TOTALtime 	= gmdate('H:i:s', array_sum($TOTALtime));
				$TOTtimeTC 	= gmdate('H:i:s', array_sum($TOTtimeTC));
				$TOT_AGENTS 	= 'AGENTS: '.$usercount;
				$TOTcalls	= array_sum($TOTcalls);
			}
						
				// Check if the user had an AUTOLOGOUT timeclock event during the time period
				$TCuserAUTOLOGOUT = ' ';
								
				$timeclock_ct = $astDB						
					->where("event", "AUTOLOGOUT")
					->where("user", $user)
					->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->getValue("vicidial_timeclock_log", "count(*)");
					
				if ($timeclock_ct > 0) {
					$TCuserAUTOLOGOUT = '*';
				}				

				$Toutput = array(
					"name" 			=> $nameARY, 
					"user" 			=> $userARY, 
					"number_of_calls" 	=> $callsARY, 
					"agent_time" 		=> $agent_timeARY, 
					"wait_time" 		=> $wait_secARY, 
					"talk_time" 		=> $talk_secARY, 
					"dispo_time" 		=> $dispo_secARY, 
					"pause_time" 		=> $pause_secARY, 
					"wrap_up" 		=> $dead_secARY, 
					"customer_time" 	=> $customerARY
				);						

				$TOPsorted_output = $Toutput;
		
				if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
					if ($file_download > 0) {
						$file_output .= "$fileToutput";
					}
				}
				
				if ($TOPsortMAX < $TOPsortTALLY) {
					$TOPsortMAX = $TOPsortTALLY;
				}
			*/	

				//$perfdetails_sql = "SELECT count(*) as calls,full_name,vicidial_users.user as user,status FROM vicidial_users,vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND vicidial_users.user=vicidial_agent_log.user $log_groupSQL AND campaign_id = '$campaign_id' GROUP BY user,full_name,status order by full_name,user,status desc limit 500000";
				// --- SIGN IN DURATION -----
				/*$sid_sql = "SELECT user, event, event_epoch as event_date FROM vicidial_user_log WHERE date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'and campaign_id = '$campaign_id'  ORDER BY event_date LIMIT $limit;";
				$login_sql = "SELECT user, event, event_date FROM vicidial_user_log WHERE date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'and campaign_id = '$campaign_id' AND event = 'LOGIN' ORDER BY event_date LIMIT $limit;";
				$logout_sql = "SELECT user, event, event_date FROM vicidial_user_log WHERE date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'and campaign_id = '$campaign_id' AND event = 'LOGOUT' ORDER BY event_date LIMIT $limit;";*/
				/*
				$sid_sql = "SELECT user, (CASE WHEN (a.date - b.date)>-1 THEN (a.date - b.date) END) as diff FROM 
						(SELECT user, event_date as date FROM vicidial_user_log WHERE date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND event = 'LOGIN' AND campaign_id = '$campaign_id') a 
						JOIN 
						(SELECT event_date as date FROM vicidial_user_log WHERE date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND event = 'LOGOUT' AND campaign_id = '$campaign_id') b;";
				$sid_query = $astDB->rawQuery($sid_sql);
				foreach($sid_query as $data){
					if(!is_null($data['diff'])){
						if(!empty($sid_data[$data['user']])){
							$sid_data[$data['user']][] = $sid_data[$data['user']][0] + $data['diff'];
						}else
						$sid_data[$data['user']][] = $data['diff'];
					}
				}*/
				//$alex[] = $sid_query;
				//$sid_data = array_map('current',$sid_data);
				//$alex[] = $sid_data;
				/*
				$sid_query = $astDB->rawQuery($sid_sql);
				$login_query = $astDB->rawQuery($login_sql);
				$logout_query = $astDB->rawQuery($logout_sql);
				
				//$alex[] = $sid_query;				

				$array_check_user = array();
				$event_login = "";
				$event_logout = "";
				$SID = array();
				foreach($sid_query as $key => $data){
					/*
					if(!in_array($data['user'], $array_check_user)){
						$array_check_user[] = $data['user'];
					}else{

					}*/
				//NEED TO ADD MULTIPLE USER LOGIN LOGOUT	
				/*	if(empty($event_login) && $data['event'] === "LOGIN"){
						$event_login = $data['event_date'];
						$alex['EVENT LOGIN'] = $event_login;
					}
					if(empty($event_logout) && ($data['event'] === "LOGOUT" ||  $data['event'] === "FORCE-LOGOUT")){
						$event_logout = $data['event_date'];
						$alex['EVENT LOGOUT'] = $event_logout;
					}
					if(!empty($event_login) && !empty($event_logout)){
						$SID[] = gmdate('H:i:s',$event_logout - $event_login); // DOESN'T TAKE IN EQUATION THE DUP USER
						$sid_sec[] = $event_logout - $event_login;
						$alex['SID'] = $event_logout - $event_login;
						$event_login = "";
		                                $event_logout = "";
					}
					
				}
				$alex[] = $SID;
/*	
				foreach($login_query as $data){
					$login_data = $data['event_date'];
				}
				
				foreach($logout_query as $data){
					$logout_data = $data['event_date'];
				}

				$alex[] = $login_data;
				$alex[] = $logout_data;			
*/
				
				// ----- TALK TIME	SPH	CALL VOLUME	AM	NI	CB ------
				$perfdetails_sql = "SELECT  vu.full_name, val.user, sum(wait_sec) as wait_sec, sum(talk_sec) as talk_sec, sum(dispo_sec) as dispo_sec, sum(pause_sec) as pause_sec, count(lead_id) as calls, GROUP_CONCAT(status) as status, sum(dead_sec) as dead_sec, (sum(talk_sec) - sum(dead_sec)) as customer FROM vicidial_agent_log val LEFT JOIN vicidial_users vu on val.user = vu.user WHERE date_format(val.event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND campaign_id IN ($imploded_camp) GROUP BY user LIMIT $limit;";
				$perf_query = $astDB->rawQuery($perfdetails_sql);
				$last_q = $perfdetails_sql;
				
			// ---- Other Statuses ---
		
				$am_sql = "SELECT status FROM vicidial_statuses WHERE answering_machine = 'Y'";
                                $ni_sql = "SELECT status FROM vicidial_statuses WHERE not_interested = 'Y'";
                                $cb_sql = "SELECT status FROM vicidial_statuses WHERE scheduled_callback = 'Y'";

                                $am_q = $astDB->rawQuery($am_sql);
                                $ni_q = $astDB->rawQuery($ni_sql);
                                $cb_q = $astDB->rawQuery($cb_sql);

				$am_array = array_map('current', $am_q);
                                $ni_array = array_map('current', $ni_q);
                                $cb_array = array_map('current', $cb_q);
				
			// ALLOCATING DATA IN VARIABLES / ARRAYS
				foreach($perf_query as $data){
					$agent_name[] = $data['full_name'];
					$talk_sec[] = convert($data['talk_sec']);
					$call_vol[] = $data['calls'];
					$statuses[] = $data['status'];
					$total_time = ($data['wait_sec'] + $data['talk_sec'] + $data['dispo_sec'] + $data['pause']);	
					// SID
					$sid[] = convert($total_time);//gmdate('H:i:s',$total_time);
					// WRAP %
					$wrap_sec = $data['dead_sec'];
					$wrap_percent[] = round(($wrap_sec/$total_time)*100).'%';
					//if($wrap > 0){$wrap_percent[] = $wrap.'%';} else{$wrap_percent[] = 0.'%';}

					// DISPO %
					$dispo_sec = $data['dispo_sec'];
					$dispo_percent[] = round(($dispo_sec/$total_time)*100).'%';
					//if($dispo > 0){$dispo_percent[] = $dispo.'%';}else{$dispo_percent[] = 0.'%';}

					// ---- SPH ----
                                	$outsales = $astDB
                                                ->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
                                                ->where("vlog.status", "SALE")
                                                ->where("vlog.call_date", array($fromDate, $toDate), "BETWEEN")
                                                ->where("vlog.campaign_id", $array_camp, "IN")
						->where("vlog.user", $data['user'])
                                                ->getValue("vicidial_log as vlog", "count(*)");
					
                                	$insales = $astDB
                                                ->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
                                                ->where("vcl.status", "SALE")
                                                ->where("vcl.call_date", array($fromDate, $toDate), "BETWEEN")
                                                ->where("vcl.campaign_id", $array_camp, "IN")
						->where("vcl.user", $data['user'])
                                                ->getValue("vicidial_closer_log  vcl", "count(*)");

                                	$SPH[] = $outsales + $insales;
	
					// COUNT AM NI and CB and put in independent variables
					$exploded_statuses = explode(',', $data['status']);
					//$counts = array_count_values($exploded_statuses);
					
					$am_count = 0;
					$ni_count = 0;
					$cb_count = 0;
					foreach($exploded_statuses as $data){
						if(in_array($data, $am_array))
							$am_count = $am_count + 1;
						if(in_array($data, $ni_array))
                                                        $ni_count = $ni_count + 1;
						if(in_array($data, $cb_array))
                                                        $cb_count = $cb_count + 1;
					}
					$am[] = $am_count; // Anwering Machine
					$ni[] = $ni_count; // Not Interested
					$cb[] = $cb_count; // Callback

				}
			//	$alex[] = $statuses;
				$apiresults = array(
						"result" => "success",
						"agent_name" => $agent_name,
						"sid" => $sid,
						"wrap" => $wrap_percent,
						"dispo" => $dispo_percent,
						"talk_sec" => $talk_sec,
						"sph" => $SPH,
						"call_vol" => $call_vol,
						"am" => $am,
						"ni" => $ni,
						"cb" => $cb
					);

				
				/*$i = 0; $j = 0;
				while ($row = $astDB->rawQuery($perfdetails_sql, MYSQLI_ASSOC)) {
					/*$calls[$i]        = $row['calls'];
                                        $full_name[$i]    = $row['full_name'];
                                        $user[$i]         = $row['user'];
                                        $status[$i]       = $row['status'];
						
                                        if ( (!preg_match("/-$status[$i]-/", $statuses)) AND (strlen($status[$i])>0) ) {
                                                $statuses     .= "$status[$i]-";
                                                $SUMstatuses  .= "$status[$i] ";
                                                $statusesARY[$j] = $status[$i];

                                                ## getting status name
                                                $var_status = $status[$i];

                                                # in default statuses
                                                $query = "
                                                    SELECT status_name FROM vicidial_statuses
                                                    WHERE status = '$var_status' LIMIT 1;
                                                ";
                                                if ($query) {
                                                    $fetch_statusname = $astDB->rawQuery($query);
                                                }

                                                if (!isset($fetch_statusname) || $fetch_statusname == NULL) {
                                                    # in custom statuses
                                                    $query = "
                                                        SELECT status_name FROM vicidial_campaign_statuses
                                                        WHERE status = '$var_status' LIMIT 1;
                                                    ";
                                                    $fetch_statusname = $astDB->rawQuery($query);
                                                }

                                                $legend[] = $status[$i]." = ".$fetch_statusname['status_name'];

                                                ## end of getting status name
                                                $SstatusesTOP .= "<th> $status[$i] </th>";
                                                $j++;
					}

                                        if (!preg_match("/-$user[$i]-/", $users)) {
                                                $users              .= "$user[$i]-";
                                                $usersARY[$k]       = $user[$i];
                                                $user_namesARY[$k]  = $full_name[$i];
                                                $k++;
                                        }
					*/
                                //        $i++;
				//}// end while
				/*
				 // BEGIN loop through each user //
                                $m = 0; $k = 0;
                                while ($m < $k) {
                                        $Suser          = $usersARY[$m];
                                        $Sfull_name     = $user_namesARY[$m];
					$Scalls         = 0;

                                        // BEGIN loop through each status //
                                        $n = 0;
                                        while ($n < $j) {
                                                $Sstatus = $statusesARY[$n];
                                                // BEGIN loop through each stat line //
                                                $i = 0;
                                                $status_found = 0;
                                                     while ($i < $perfdetails_sql) {
                                                        if ( ($Suser=="$user[$i]") AND ($Sstatus == "$status[$i]") ) {
                                                            $SstatusesMID[$m] .= "<td> $calls[$i] </td>";
                                                            $status_found++;
                                                        }
                                                        $i++;
                                                     }
                                                // END loop through each stat line //
                                                $n++;
                                        }
					// END loop through each status //

					$Moutput = "<tr>
                                                <td> $Sfull_name </td>
                                                $SstatusesMID[$m]
                                                </tr>";
					$MIDsorted_output[$m] = $Moutput;

					$m++			
				}*/
//		$apiresults = array("data" => $alex);
		/*
				$apiresults = array(
					"result" 		=> "success",
					//"data" => $alex
					/*, 
					"TOPsorted_output" 	=> $TOPsorted_output, 
					"PC_statuses"		=> $pcs_data, //Pause Code data
					"TOTwait" 		=> $TOTwait, 
					"TOTtalk" 		=> $TOTtalk, 
					"TOTdispo" 		=> $TOTdispo, 
					"TOTpause" 		=> $TOTpause, 
					"TOTdead" 		=> $TOTdead, 
					"TOTcustomer" 		=> $TOTcustomer, 
					"TOTALtime" 		=> $TOTALtime, 
					"TOTtimeTC" 		=> $TOTtimeTC, 
					"TOT_AGENTS" 		=> $TOT_AGENTS, 
					"TOTcalls" 		=> $TOTcalls,
					//"SstatusesBSUM"         => $SstatusesBSUM,
					//"MIDsorted_output"	=> $MIDsorted_output,
					//"legend"		=> $legend*/
	//			);
				
		//		return $apiresults;				
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
