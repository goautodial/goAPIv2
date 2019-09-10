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
error_reporting(E_ERROR | E_PARSE);
    include_once("goAPI.php");

	$fromDate 	= (empty($_REQUEST['fromDate']) ? date("Y-m-d")." 00:00:00" : $astDB->escape($_REQUEST['fromDate']));
	$toDate 	= (empty($_REQUEST['toDate']) ? date("Y-m-d")." 23:59:59" : $astDB->escape($_REQUEST['toDate']));
	$campaign_id 	= $astDB->escape($_REQUEST['campaignID']);
	$request 	= $astDB->escape($_REQUEST['request']);
	$limit		= 100;
    
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults = array(
			"result" => "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults = array(
			"result" => "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults = array(
			"result" => "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg = error_handle("40001");
        $apiresults = array(
			"code" => "40001",
			"result" => $err_msg
		);
	} else {            
		// check if goUser and goPass are valid
		$fresults = $astDB
			->where("user", $goUser)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess = $astDB->getRowCount();
		$userlevel = $fresults["user_level"];
		//$apiresults = array("data" => $alex);	

		if ($goapiaccess > 0 && $userlevel > 7) {
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant	= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
				
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}
			
			if ("ALL" === strtoupper($campaign_id)) {
				$SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");

                                foreach($SELECTQuery as $camp_val){
                                        $array_camp[] = $camp_val["campaign_id"];
                                }
                        }else{
                             $array_camp[] = $campaign_id;
                        }
                        $imploded_camp = "'".implode("','", $array_camp)."'";
			
			// ------------ S T A R T -----------------------
				$statusesFILE = "";
                                $statuses = '-';
                                $statusesARY[0] = "";
                                $j = 0;
                                $users = '-';
                                $usersARY = array();
                                $user_namesARY = array();
                                $k = 0;

                                if ($date_diff <= 0) {
                                        $filters = "AND pause_sec < 65000 AND wait_sec<65000 AND talk_sec<65000 AND dispo_sec<65000 ";
                                }

                                $perfdetails_sql = "SELECT count(*) as calls,sum(talk_sec) as talk,full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sum(wait_sec) as wait_sec,sum(dispo_sec) as dispo_sec,status,sum(dead_sec) as dead_sec FROM vicidial_users,vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND vicidial_users.user=vicidial_agent_log.user $log_groupSQL AND campaign_id IN ($imploded_camp) GROUP BY user,full_name,status order by full_name,user,status desc limit 500000";
                                $rows_to_print = $astDB->rawQuery($perfdetails_sql);
                                $i = 0;
				foreach($rows_to_print as $row){
                                        $calls[$i] = $row['calls'];
                                        $talk_sec[$i] = $row['talk'];
                                        $full_name[$i] = $row['full_name'];
                                        $Puser[$i] = $row['user'];
                                        $pause_sec[$i] = $row['pause_sec'];
                                        $wait_sec[$i] = $row['wait_sec'];
                                        $dispo_sec[$i] = $row['dispo_sec'];
                                        $status[$i] = $row['status'];
                                        $dead_sec[$i] = $row['dead_sec'];
                                        $customer_sec[$i] = ($talk_sec[$i] - $dead_sec[$i]);

                                        if ($customer_sec[$i] < 1) {
                                                $customer_sec[$i] = 0;
                                        }

                                        if ( (!preg_match("/-$status[$i]-/", $statuses)) AND (strlen($status[$i])>0) ) {
                                                $statusesFILE .= ",$status[$i]";
                                                $statuses .= "$status[$i]-";
                                                $SUMstatuses .= "$status[$i] ";
                                                $statusesARY[$j] = $status[$i];

                                                ## getting status name
                                                $var_status = $status[$i];

                                                # in default statuses
						$astDB->where("status", $var_status);
						$default_status = $astDB->getOne("vicidial_statuses", "status_name");

                                                if ($default_status) {
                                                        $fetch_statusname = $default_status;
                                                }


                                                if (!isset($fetch_statusname) || $fetch_statusname == NULL) {
                                                        # in custom statuses
							$astDB->where("status", $var_status);
	                                                $camp_status = $astDB->getOne("vicidial_campaign_statuses", "status_name");
                                                        $fetch_statusname = $camp_status;
                                                }

                                                $legend[] = $status[$i]." = ".$fetch_statusname['status_name'];

                                                ## end of getting status name
                                                $SstatusesTOP .= "<th> $status[$i] </th>";
                                                $j++;
                                        }

                                        if (!preg_match("/-".$Puser[$i]."-/", $users)) {
                                                $users .= $row['user']."-";
                                                $usersARY[$k] = $row['user'];
						$userIDARY[$k] = $row['user'];
                                                $user_namesARY[$k] = $full_name[$i];
                                                $k++;
                                        }
                                        $i++;
                                }//end foreach loop
                                if ($file_download > 0) {
                                        $file_output = "CAMPAIGN,$campaignID - ".$resultu->campaign_name."\n";
                                        $file_output .= "DATE RANGE,$fromDate TO $toDate\n\n";
                                        $file_output .= "USER NAME,ID,CALLS,AGENT TIME,PAUSE,PAUSE AVG,WAIT,WAIT AVG,TALK,TALK AVG,DISPO,DISPO AVG,WRAPUP,WRAPUP AVG,CUSTOMER,CUST AVG $statusesFILE\n";
                                }
                                // BEGIN loop through each user //
                                $m = 0;
                                while ($m < $k) {
                                        $Suser = $usersARY[$m];
					$userID = $userIDARY[$m];
                                        $Sfull_name = $user_namesARY[$m];
                                        $Stime = 0;
                                        $Scalls = 0;
                                        $Stalk_sec = 0;
                                        $Spause_sec = 0;
                                        $Swait_sec = 0;
                                        $Sdispo_sec = 0;
                                        $Sdead_sec = 0;
                                        $Scustomer_sec = 0;
                                        $SstatusesHTML = "";
                                        $SstatusesFILE = "";

                                        // BEGIN loop through each status //
                                        $n = 0;

                                        while ($n < $j) {
                                                $Sstatus = $statusesARY[$n];
                                                $SstatusTXT = "";
                                                // BEGIN loop through each stat line //
                                                $i = 0;
                                                $status_found = 0;
                                                foreach ($rows_to_print as $i => $val) {
                                                        if ( ($Suser == $Puser[$i]) && ($Sstatus == $status[$i]) ) {
                                                                $Scalls = ($Scalls + $calls[$i]);
                                                                $Stalk_sec = ($Stalk_sec + $talk_sec[$i]);
                                                                $Spause_sec = ($Spause_sec + $pause_sec[$i]);
                                                                $Swait_sec = ($Swait_sec + $wait_sec[$i]);
                                                                $Sdispo_sec = ($Sdispo_sec + $dispo_sec[$i]);
                                                                $Sdead_sec = ($Sdead_sec + $dead_sec[$i]);
                                                                $Scustomer_sec = ($Scustomer_sec + $customer_sec[$i]);
                                                                $SstatusesFILE .= ",$calls[$i]";
                                                                $SstatusesMID[$m] .= "<td> $calls[$i] </td>";
                                                                $status_found++;
                                                        }

                                                        $i++;
                                                }

                                                if ($status_found < 1) {
                                                        $SstatusesFILE .= ",0";
                                                        $SstatusesMID[$m] .= "<td> 0 </td>";
                                                }
                                                // END loop through each stat line //
                                                $n++;
                                        }
                                        // END loop through each status //
                                        
					$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
                                        $TOTcalls = ($TOTcalls + $Scalls);
                                        $TOTtime = ($TOTtime + $Stime);
                                        $TOTtotTALK = ($TOTtotTALK + $Stalk_sec);
                                        $TOTtotWAIT = ($TOTtotWAIT + $Swait_sec);
                                        $TOTtotPAUSE = ($TOTtotPAUSE + $Spause_sec);
                                        $TOTtotDISPO = ($TOTtotDISPO + $Sdispo_sec);
                                        $TOTtotDEAD = ($TOTtotDEAD + $Sdead_sec);
                                        $TOTtotCUSTOMER = ($TOTtotCUSTOMER + $Scustomer_sec);
                                        $Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);

                                        if ( ($Scalls > 0) AND ($Stalk_sec > 0) ) {
                                                $Stalk_avg = ($Stalk_sec/$Scalls);
                                        } else {
                                                $Stalk_avg = 0;
                                        }

                                        if ( ($Scalls > 0) AND ($Spause_sec > 0) ) {
                                                $Spause_avg = ($Spause_sec/$Scalls);
                                        } else {
                                                $Spause_avg = 0;
                                        }

                                        if ( ($Scalls > 0) AND ($Swait_sec > 0) ) {
                                                $Swait_avg = ($Swait_sec/$Scalls);
                                        } else {
                                                $Swait_avg = 0;
                                        }

                                        if ( ($Scalls > 0) AND ($Sdispo_sec > 0) ) {
                                                $Sdispo_avg = ($Sdispo_sec/$Scalls);
                                        } else {
                                                $Sdispo_avg = 0;
                                        }

                                        if ( ($Scalls > 0) AND ($Sdead_sec > 0) ) {
                                                $Sdead_avg = ($Sdead_sec/$Scalls);
                                        } else {
                                                $Sdead_avg = 0;
                                        }

                                        if ( ($Scalls > 0) AND ($Scustomer_sec > 0) ) {
                                                $Scustomer_avg = ($Scustomer_sec/$Scalls);
                                        } else {
                                                $Scustomer_avg = 0;
                                        }

                                        $RAWuser = $Suser;
                                        $RAWcalls = $Scalls;

                                        $pfUSERtime_MS = convert($Stime);
                                        $pfUSERtotTALK_MS = convert($Stalk_sec);
                                        $pfUSERavgTALK_MS = convert($Stalk_avg);
                                        $pfUSERtotPAUSE_MS = convert($Spause_sec);
                                        $pfUSERavgPAUSE_MS = convert($Spause_avg);
                                        $pfUSERtotWAIT_MS = convert($Swait_sec);
                                        $pfUSERavgWAIT_MS = convert($Swait_avg);
                                        $pfUSERtotDISPO_MS = convert($Sdispo_sec);
                                        $pfUSERavgDISPO_MS = convert($Sdispo_avg);
                                        $pfUSERtotDEAD_MS = convert($Sdead_sec);
                                        $pfUSERavgDEAD_MS = convert($Sdead_avg);
                                        $pfUSERtotCUSTOMER_MS = convert($Scustomer_sec);
                                        $pfUSERavgCUSTOMER_MS = convert($Scustomer_avg);

                                        $PAUSEtotal[$m] = $pfUSERtotPAUSE_MS;

                                        if ($file_download > 0) {
                                                $fileToutput = "$Sfull_name,=\"$Suser\",$Scalls,$pfUSERtime_MS,$pfUSERtotPAUSE_MS,$pfUSERavgPAUSE_MS,$pfUSERtotWAIT_MS,$pfUSERavgWAIT_MS,$pfUSERtotTALK_MS,$pfUSERavgTALK_MS,$pfUSERtotDISPO_MS,$pfUSERavgDISPO_MS,$pfUSERtotDEAD_MS,$pfUSERavgDEAD_MS,$pfUSERtotCUSTOMER_MS,$pfUSERavgCUSTOMER_MS$SstatusesFILE\n";
                                        }

                                        if ($x == 0) {
                                                $bgcolor = "#E0F8E0";
                                                $x = 1;
                                        } else {
                                                $bgcolor = "#EFFBEF";
                                                $x = 0;
                                        }

                                        $Toutput = "<tr>
                                                <td> $Sfull_name </td>
                                                <td> $userID </td>
                                                <td> $Scalls </td>
                                                <td> $pfUSERtime_MS </td>
                                                <td> $pfUSERtotPAUSE_MS </td>
                                                <td> $pfUSERavgPAUSE_MS </td>
                                                <td> $pfUSERtotWAIT_MS </td>
                                                <td> $pfUSERavgWAIT_MS </td>
                                                <td> $pfUSERtotTALK_MS </td>
                                                <td> $pfUSERavgTALK_MS </td>
                                                <td> $pfUSERtotDISPO_MS </td>
                                                <td> $pfUSERavgDISPO_MS </td>
                                                <td> $pfUSERtotDEAD_MS </td>
                                                <td> $pfUSERavgDEAD_MS </td>
                                                <td> $pfUSERtotCUSTOMER_MS </td>
                                                <td> $pfUSERavgCUSTOMER_MS </td>
                                                </tr>";

                                        $Moutput = "<tr>
                                                <td> $Sfull_name </td>
                                                $SstatusesMID[$m]
                                                </tr>";

                                        $TOPsorted_output[] = $Toutput;
                                        $MIDsorted_output[] = $Moutput;
                                        $TOPsorted_outputFILE[] = $fileToutput;

                                        if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
                                                if ($file_download > 0) {
                                                        $file_output .= "$fileToutput";
                                                }
					        $m++;
                                        }
                                }
                                // END loop through each user //

                                // BEGIN sort through output to display properly //
                                if (preg_match("/ID|TIME|LEADS/",$stage)) {
                                        if (preg_match("/ID/",$stage)) {
                                                sort($TOPsort, SORT_NUMERIC);
                                        }

                                        if (preg_match("/TIME|LEADS/",$stage)) {
                                                rsort($TOPsort, SORT_NUMERIC);
                                        }

                                        $m = 0;

                                        while ($m < $k) {
                                                $sort_split = explode("-----",$TOPsort[$m]);
                                                $i = $sort_split[1];
                                                $sort_order[$m] = "$i";

                                                if ($file_download > 0) {
                                                        $file_output .= "$TOPsorted_outputFILE[$i]";
                                                }

                                                $m++;
                                        }
                                }
                                // END sort through output to display properly //
                                //## LAST LINE FORMATTING ////##
                                // BEGIN loop through each status //
                                $SUMstatusesHTML = "";
                                $SUMstatusesFILE = "";
                                $n = 0;

                                while ($n < $j) {
                                        $Scalls = 0;
                                        $Sstatus = $statusesARY[$n];
                                        $SUMstatusTXT = "";
                                        // BEGIN loop through each stat line //
                                        $i = 0;
                                        $status_found = 0;

                                        foreach ($rows_to_print as $i => $val) {
                                                if ($Sstatus == "$status[$i]") {
                                                $Scalls = ($Scalls + $calls[$i]);
                                                        $status_found++;
                                                }

                                                $i++;
                                        }

                                        // END loop through each stat line //
                                        if ($status_found < 1) {
                                                $SUMstatusesFILE .= ",0";
                                                $SstatusesSUM .= "<th> 0 </th>";
                                        } else {
                                                $SUMstatusesFILE .= ",$Scalls";
                                                $SstatusesSUM .= "<th> $Scalls </th>";
                                        }

                                        $n++;
                                }
                                // END loop through each status //
                                $TOT_AGENTS = '<th nowrap>AGENTS: '.$m.'</th>';

                                if ($TOTtotTALK < 1) {
                                        $TOTavgTALK = '0';
                                } else {
                                        $TOTavgTALK = ($TOTtotTALK / $TOTcalls);
                                }

                                if ($TOTtotDISPO < 1) {
                                        $TOTavgDISPO = '0';
                                } else {
                                        $TOTavgDISPO = ($TOTtotDISPO / $TOTcalls);
                                }

                                if ($TOTtotDEAD < 1) {
                                        $TOTavgDEAD = '0';
                                } else {
                                        $TOTavgDEAD = ($TOTtotDEAD / $TOTcalls);
                                }

                                if ($TOTtotPAUSE < 1) {
                                        $TOTavgPAUSE = '0';
                                } else {
                                        $TOTavgPAUSE = ($TOTtotPAUSE / $TOTcalls);
                                }

                                if ($TOTtotWAIT < 1) {
                                        $TOTavgWAIT = '0';
                                } else {
                                        $TOTavgWAIT = ($TOTtotWAIT / $TOTcalls);
                                }

                                if ($TOTtotCUSTOMER < 1) {
                                        $TOTavgCUSTOMER = '0';
                                } else {
                                        $TOTavgCUSTOMER = ($TOTtotCUSTOMER / $TOTcalls);
                                }

                                $TOTcalls = '<th nowrap>'.$TOTcalls.'</th>';
                                $TOTtime_MS = '<th nowrap>'.convert($TOTtime).'</th>';
                                $TOTtotTALK_MS = '<th nowrap>'.convert($TOTtotTALK).'</th>';
				$TOTtotPAUSE_MS = '<th nowrap>'.convert($TOTtotPAUSE).'</th>';
				$TOTavgPAUSE_MS = '<th nowrap>'.convert($TOTavgPAUSE).'</th>';
				$TOTtotWAIT_MS = '<th nowrap>'.convert($TOTtotWAIT).'</th>';
				$TOTavgWAIT_MS = '<th nowrap>'.convert($TOTavgWAIT).'</th>';
				$TOTtotTALK_MS = '<th nowrap>'.convert($TOTtotTALK).'</th>';
				$TOTavgTALK_MS = '<th nowrap>'.convert($TOTavgTALK).'</th>';
                                $TOTtotDISPO_MS = '<th nowrap>'.convert($TOTtotDISPO).'</th>';
				$TOTavgDISPO_MS = '<th nowrap>'.convert($TOTavgDISPO).'</th>';
                                $TOTtotDEAD_MS = '<th nowrap>'.convert($TOTtotDEAD).'</th>';
				$TOTavgDEAD_MS = '<th nowrap>'.convert($TOTavgDEAD).'</th>';
                                $TOTtotCUSTOMER_MS = '<th nowrap>'.convert($TOTtotCUSTOMER).'</th>';
				$TOTavgCUSTOMER_MS = '<th nowrap>'.convert($TOTavgCUSTOMER).'</th>';

                                if ($file_download > 0) {
                                        $file_output .= "TOTAL AGENTS: $TOT_AGENTS,$TOTcalls,$TOTtime_MS,$TOTtotPAUSE_MS,$TOTavgPAUSE_MS,$TOTtotWAIT_MS,$TOTavgWAIT_MS,$TOTtotTALK_MS,$TOTavgTALK_MS,$TOTtotDISPO_MS,$TOTavgDISPO_MS,$TOTtotDEAD_MS,$TOTavgDEAD_MS,$TOTtotCUSTOMER_MS,$TOTavgCUSTOMER_MS$SUMstatusesFILE\n";
                                }


			//----------------- BOTTOM TABLE START -------------
                                $sub_statuses = '-';
                                $sub_statusesTXT = "";
                                $sub_statusesFILE = "";
                                $sub_statusesHEAD = "";
                                $sub_statusesHTML = "";
                                $sub_statusesARY = array();
                                $j = 0;
                                $PCusers = '-';
                                $PCusersARY = array();
                                $PCuser_namesARY = array();
                                $k = 0;
				$pause_condition = "AND pause_sec < 65000";

                                $pause_sql = "SELECT full_name,vicidial_users.user as user, sum(pause_sec) as pause_sec,sub_status, sum(wait_sec + talk_sec + dispo_sec) as non_pause_sec FROM vicidial_users,vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'  AND vicidial_users.user = vicidial_agent_log.user $log_groupSQL AND campaign_id IN ($imploded_camp) GROUP BY user ORDER BY full_name,user,sub_status desc limit 1000";
                                $subs_to_print = $astDB->rawQuery($pause_sql);

				$i = 0;
				foreach($subs_to_print as $Brow){
                                        $PCfull_name[$i] = $Brow['full_name'];
                                        $PCuser[$i] = $Brow['user'];
                                        $PCpause_sec[$i] = $Brow['pause_sec'];
                                        $sub_status[$i] = $Brow['sub_status'];
                                        $PCnon_pause_sec[$i] = $Brow['non_pause_sec'];

                                        if (!preg_match("/-$sub_status[$i]-/", $sub_statuses)) {
                                                $sub_statuses .= "$sub_status[$i]-";
                                                $sub_statusesFILE .= ",$sub_status[$i]";
                                                $sub_statusesARY[$j] = $sub_status[$i];
                                                $SstatusesBOT .= "<th> $sub_status[$i] </th>";
                                                $j++;
                                        }
                                        if (!preg_match("/-".$PCuser[$i]."-/", $PCusers)) {
                                                $PCusers .= $PCuser[$i]."-";
                                                $PCusersARY[$k] = $PCuser[$i];
                                                $PCuser_namesARY[$k] = $PCfull_name[$i];
                                                $k++;
                                        }
                                        $i++;
                                }

                                if ($file_download > 0) {
                                        $file_output .= "\n\nUSER NAME,ID,TOTAL,NONPAUSE,PAUSE,$sub_statusesFILE\n";
                                }

                                // BEGIN loop through each user //
                                $m = 0;
                                $Suser_ct = count($usersARY);
                                $TOTtotNONPAUSE = 0;
                                $TOTtotTOTAL = 0;
                               	$alex["k"] = $k; 
				while ($m < $k) {
                                        $d = 0;
                                        while ($d < $Suser_ct) {
                                                if ($usersIDARY[$d] === "$PCusersARY[$m]") {
                                                        $pcPAUSEtotal = $PAUSEtotal[$d];
                                                }

                                                $d++;
                                        }

                                        $Suser = $PCusersARY[$m];
                                        $Sfull_name = $PCuser_namesARY[$m];
                                        $Spause_sec = 0;
                                        $Snon_pause_sec = 0;
                                        $Stotal_sec = 0;
                                        $SstatusesHTML = "";
                                        $Ssub_statusesFILE = "";

                                        // BEGIN loop through each status //
                                        $n = 0;

                                        while ($n < $j) {
                                                $Sstatus = $sub_statusesARY[$n];
                                                $SstatusTXT = "";
                                                // BEGIN loop through each stat line //
                                                $i = 0;
                                                $status_found = 0;

                                                foreach($subs_to_print as $i => $val) {
                                                        if ( ($Suser == "$PCuser[$i]") AND ($Sstatus == "$sub_status[$i]") ) {
                                                                $Spause_sec = ($Spause_sec + $PCpause_sec[$i]);
                                                                $Snon_pause_sec = ($Snon_pause_sec + $PCnon_pause_sec[$i]);
                                                                $Stotal_sec = ($Stotal_sec + $PCnon_pause_sec[$i] + $PCpause_sec[$i]);

                                                                $USERcodePAUSE_MS = convert($PCpause_sec[$i]);
                                                                $pfUSERcodePAUSE_MS = sprintf("%6s", $USERcodePAUSE_MS);

                                                                $Ssub_statusesFILE .= ",$USERcodePAUSE_MS";
                                                                $SstatusesBOTR[$m] .= "<td> $USERcodePAUSE_MS </td>";
                                                                $status_found++;
                                                        }

                                                        $i++;
                                                }

                                                if ($status_found < 1) {
                                                        $Ssub_statusesFILE .= ",0";
                                                        $SstatusesBOTR[$m] .= "<td> 0:00 </td>";
                                                }
                                                // END loop through each stat line //
                                                $n++;
                                        }
                                        // END loop through each status //
                                        $TOTtotPAUSE = ($TOTtotPAUSE + $Spause_sec);
                                        $TOTtotNONPAUSE = ($TOTtotNONPAUSE + $Snon_pause_sec);
                                        $TOTtotTOTAL = ($TOTtotTOTAL + $Stotal_sec);

                                        $pfUSERtotPAUSE_MS = convert($Spause_sec);
                                        $pfUSERtotNONPAUSE_MS = convert($Snon_pause_sec);
                                        $pfUSERtotTOTAL_MS = convert($Stotal_sec);

                                        if ($file_download > 0) {
                                                $fileToutput = "$Sfull_name,=\"$Suser\",$pfUSERtotTOTAL_MS,$pfUSERtotNONPAUSE_MS,$pfUSERtotPAUSE_MS,$Ssub_statusesFILE\n";
                                        }

                                        if ($x == 1) {
                                                $bgcolor = "#E0F8E0";
                                                $x = 0;
                                        } else {
                                                $bgcolor = "#EFFBEF";
                                                $x = 1;
                                        }

                                        $Boutput = "<tr>
                                                <td> $Sfull_name </td>
                                                <td> $Suser </td>
                                                <td> $pfUSERtotTOTAL_MS </td>
                                                <td> $pfUSERtotNONPAUSE_MS </td>
                                                <td> $pfUSERtotPAUSE_MS </td>
                                                </tr>";

                                        $BOTsorted_output[$m] = $Boutput;

                                        if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
                                                if ($file_download > 0) {
                                                        $file_output .= "$fileToutput";
                                                }

                                                $m++;
                                        }
                                }
                                // END loop through each user //

                                // BEGIN sort through output to display properly //
                                if (preg_match("/ID|TIME|LEADS/",$stage)) {
                                        $n = 0;
                                        while ($n <= $m) {
                                                $i = $sort_order[$m];
                                                if ($file_download > 0) {
                                                        $file_output .= "$TOPsorted_outputFILE[$i]";
                                                }
                                                $m--;
                                        }
                                }
                                // END sort through output to display properly //
                                //## LAST LINE FORMATTING ////##
                                // BEGIN loop through each status //
                                $SUMstatusesHTML = "";
                                $SUMsub_statusesFILE = "";
                                $TOTtotPAUSE = 0;
                                $n = 0;

                                while ($n < $j) {
                                        $Scalls = 0;
                                        $Sstatus = $sub_statusesARY[$n];
                                        $SUMstatusTXT = "";
                                        // BEGIN loop through each stat line //
                                        $i = 0;
                                        $status_found = 0;

                                        foreach ($subs_to_print as $i => $val) {
                                                if ($Sstatus == "$sub_status[$i]") {
                                                        $Scalls = ($Scalls + $PCpause_sec[$i]);
                                                        $status_found++;
                                                }

                                                $i++;
                                        }
                                        // END loop through each stat line //
                                        if ($status_found < 1) {
                                                $SUMsub_statusesFILE .= ",0";
                                                $SstatusesBSUM .= "<th nowrap> 0:00 </th>";
                                        } else {
                                                $TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);
                                                $USERsumstatPAUSE_MS = convert($Scalls);
                                                $SUMsub_statusesFILE .= ",$USERsumstatPAUSE_MS";
                                                $SstatusesBSUM .= "<th nowrap> $USERsumstatPAUSE_MS </th>";
                                        }

                                        $n++;
                                }
                                // END loop through each status //
                                $TOT_AGENTS = '<th nowrap>AGENTS: '.$m.'</th>';
                                $TOTtotPAUSEB_MS = '<th nowrap>'.convert($TOTtotPAUSE).'</th>';
                                $TOTtotNONPAUSE_MS = '<th nowrap>'.convert($TOTtotNONPAUSE).'</th>';
                                $TOTtotTOTAL_MS = '<th nowrap>'.convert($TOTtotTOTAL).'</th>';

                                if ($file_download > 0) {
                                        $file_output .= "TOTAL AGENTS: $TOT_AGENTS,$TOTtotTOTAL_MS,$TOTtotNONPAUSE_MS,$TOTtotPAUSE_MS,$SUMsub_statusesFILE\n";
                                }

                                $apiresults = array(
                                        "result"                => "success",
                                        "TOPsorted_output"      => $TOPsorted_output,
                                        "BOTsorted_output"      => $BOTsorted_output,
                                        "TOPsorted_outputFILE"  => $TOPsorted_outputFILE,
                                        "TOTwait"               => $TOTwait,
                                        "TOTtalk"               => $TOTtalk,
                                        "TOTdispo"              => $TOTdispo,
                                        "TOTpause"              => $TOTpause,
                                        "TOTdead"               => $TOTdead,
                                        "TOTcustomer"           => $TOTcustomer,
                                        "TOTALtime"             => $TOTALtime,
                                        "TOTtimeTC"             => $TOTtimeTC,
                                        "sub_statusesTOP"       => $sub_statusesTOP,
                                        "SUMstatuses"           => $SUMstatuses,
                                        "TOT_AGENTS"            => $TOT_AGENTS,
                                        "TOTcalls"              => $TOTcalls,
                                        "TOTtime_MS"            => $TOTtime_MS,
                                        "TOTtotTALK_MS"         => $TOTtotTALK_MS,
                                        "TOTtotDISPO_MS"        => $TOTtotDISPO_MS,
                                        "TOTtotDEAD_MS"         => $TOTtotDEAD_MS,
                                        "TOTtotPAUSE_MS"        => $TOTtotPAUSE_MS,
                                        "TOTtotWAIT_MS"         => $TOTtotWAIT_MS,
                                        "TOTtotCUSTOMER_MS"     => $TOTtotCUSTOMER_MS,
                                        "TOTavgTALK_MS"         => $TOTavgTALK_MS,
                                        "TOTavgDISPO_MS"        => $TOTavgDISPO_MS,
                                        "TOTavgDEAD_MS"         => $TOTavgDEAD_MS,
                                        "TOTavgPAUSE_MS"        => $TOTavgPAUSE_MS,
                                        "TOTavgWAIT_MS"         => $TOTavgWAIT_MS,
                                        "TOTavgCUSTOMER_MS"     => $TOTavgCUSTOMER_MS,
                                        "TOTtotTOTAL_MS"        => $TOTtotTOTAL_MS,
                                        "TOTtotNONPAUSE_MS"     => $TOTtotNONPAUSE_MS,
                                        "TOTtotPAUSEB_MS"       => $TOTtotPAUSEB_MS,
                                        "MIDsorted_output"      => $MIDsorted_output,
                                        "SstatusesTOP"          => $SstatusesTOP,
                                        "SstatusesSUM"          => $SstatusesSUM,
                                        "SstatusesBOT"          => $SstatusesBOT,
                                        "SstatusesBOTR"         => $SstatusesBOTR,
                                        "SstatusesBSUM"         => $SstatusesBSUM,
                                        "Legend"                => $legend,
                                        "alex"                 => $alex
                                );

		} else {
			$err_msg= error_handle("10001");
			$apiresults = array(
				"code" => "10001", 
				"result" => $err_msg
			);		
		}
	}

?>
