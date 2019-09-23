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
    $pageTitle 											= strtolower($astDB->escape($_REQUEST['pageTitle']));
    $fromDate 											= (empty($_REQUEST['fromDate']) ? date("Y-m-d")." 00:00:00" : $astDB->escape($_REQUEST['fromDate']));
    $toDate 											= (empty($_REQUEST['toDate']) ? date("Y-m-d")." 23:59:59" : $astDB->escape($_REQUEST['toDate']));
    $campaign_id 										= $astDB->escape($_REQUEST['campaignID']);
    $request 											= $astDB->escape($_REQUEST['request']);
	$defPage 											= "stats";	

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
	
		//ALL CAMPAIGNS
                        if ("ALL" === strtoupper($campaign_id)) {
                                $SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");

                                foreach($SELECTQuery as $camp_val){
                                        $array_camp[] = $camp_val["campaign_id"];
                                }
                        }else{
                                $array_camp[] = $campaign_id;
                        }
                        $imploded_camp = "'".implode("','", $array_camp)."'";
	
		if ($goapiaccess > 0 && $userlevel > 7) {
			// Agent Statistics
			if ($pageTitle == 'stats') {			
				if ($log_group !== "ADMIN") {
                                        $ul = "AND user_group = '$log_group'";
                                } else {
                                        $ul = "";
                                }
	
				if ($request == 'daily') {
					$stringv = go_getall_closer_campaigns($campaign_id, $astDB);
					$closerCampaigns = " AND campaign_id IN ($stringv) ";
					$vcloserCampaigns = " AND vclog.campaign_id IN ($stringv) ";
					$call_time = go_get_calltimes($campaign_id, $astDB);
					
					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL = "UNION select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_closer_log where 
#length_in_sec>'0' and 
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul $closerCampaigns group by cdate";
						$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where 
#length_in_sec>'0' and 
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul";
						$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log where 
#length_in_sec>'0' and 
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul group by status";
					}
					
					// Total Calls Made					
					$qtotalcallsmade = $astDB->rawQuery("select cdate, sum(Hour0) as 'Hour0', sum(Hour1) as 'Hour1', sum(Hour2) as 'Hour2', sum(Hour3) as 'Hour3', sum(Hour4) as 'Hour4', sum(Hour5) as 'Hour5', sum(Hour6) as 'Hour6', sum(Hour7) as 'Hour7', sum(Hour8) as 'Hour8', sum(Hour9) as 'Hour9', sum(Hour10) as 'Hour10', sum(Hour11) as 'Hour11', sum(Hour12) as 'Hour12', sum(Hour13) as 'Hour13', sum(Hour14) as 'Hour14', sum(Hour15) as 'Hour15', sum(Hour16) as 'Hour16', sum(Hour17) as 'Hour17', sum(Hour18) as 'Hour18', sum(Hour19) as 'Hour19', sum(Hour20) as 'Hour20', sum(Hour21) as 'Hour21', sum(Hour22) as 'Hour22', sum(Hour23) as 'Hour23' from (select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_log where 
#length_in_sec>'0' and 
campaign_id IN ($imploded_camp) and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul group by cdate $MunionSQL) t group by cdate;");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$cdate[] 					= $row['cdate'];
							$hour0[] 					= $row['Hour0'];
							$hour1[] 					= $row['Hour1'];
							$hour2[] 					= $row['Hour2'];
							$hour3[] 					= $row['Hour3'];
							$hour4[] 					= $row['Hour4'];
							$hour5[] 					= $row['Hour5'];
							$hour6[] 					= $row['Hour6'];
							$hour7[] 					= $row['Hour7'];
							$hour8[] 					= $row['Hour8'];
							$hour9[] 					= $row['Hour9'];
							$hour10[] 					= $row['Hour10'];
							$hour11[] 					= $row['Hour11'];
							$hour12[] 					= $row['Hour12'];
							$hour13[] 					= $row['Hour13'];
							$hour14[] 					= $row['Hour14'];
							$hour15[] 					= $row['Hour15'];
							$hour16[] 					= $row['Hour16'];
							$hour17[] 					= $row['Hour17'];
							$hour18[] 					= $row['Hour18'];
							$hour19[] 					= $row['Hour19'];
							$hour20[] 					= $row['Hour20'];
							$hour21[] 					= $row['Hour21'];
							$hour22[] 					= $row['Hour22'];
							$hour23[] 					= $row['Hour23'];							
						}						
					}	
					
					$data_calls 						= array(
						"cdate" 							=> $cdate, 
						"hour0" 							=> $hour0, 
						"hour1" 							=> $hour1, 
						"hour2" 							=> $hour2, 	
						"hour3" 							=> $hour3, 
						"hour4" 							=> $hour4, 
						"hour5" 							=> $hour5, 
						"hour6" 							=> $hour6, 
						"hour7" 							=> $hour7, 
						"hour8"	 							=> $hour8, 
						"hour9" 							=> $hour9, 
						"hour10" 							=> $hour10, 
						"hour11" 							=> $hour11,
						"hour12"	 						=> $hour12, 
						"hour13" 							=> $hour13, 
						"hour14" 							=> $hour14, 
						"hour15" 							=> $hour15, 
						"hour16" 							=> $hour16, 
						"hour17"	 						=> $hour17, 
						"hour18" 							=> $hour18, 
						"hour19" 							=> $hour19, 
						"hour20" 							=> $hour20, 
						"hour21" 							=> $hour21, 
						"hour22" 							=> $hour22, 
						"hour23" 							=> $hour23
					);					
					
					$astDB->rawQuery("select phone_number from vicidial_log vl where 
#length_in_sec>'0' and 
campaign_id IN ($imploded_camp) and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul $TunionSQL");
					
					$total_calls = $astDB->getRowCount();
					
					// Total Number of Leads
					/*$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaign_id)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();*/

					$astDB->rawQuery("SELECT vl.list_id FROM  vicidial_list as vl INNER JOIN vicidial_lists as vlo ON vl.list_id = vlo.list_id WHERE vlo.campaign_id IN ($imploded_camp)");

					$total_leads = $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $array_camp, "IN")
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo", "vl.list_id");
					
					$total_new							= $astDB->getRowCount();
						
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $array_camp, "IN")
						->where("date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate'")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));					
					
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}											
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);
					
					// Disposition of Calls
					$astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where 
#length_in_sec>'0' and
call_date between '$fromDate' and '$toDate' $ul and campaign_id IN ($imploded_camp) group by status $DunionSQL) t group by status;");
					
					$total_status						= $astDB->getRowCount();
					$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where 
#length_in_sec>'0' and 
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul and campaign_id IN ($imploded_camp) group by status $DunionSQL) t group by status;");
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 							=> $status, 
						"status_name" 						=> $status_name, 
						"ccount" 							=> $ccount, 
						"query" 							=> $qtotalcallsmade
					);
				}
				
				if ($request == 'weekly') {
					$stringv	= go_getall_closer_campaigns($campaign_id, $astDB);
					$closerCampaigns 	= " AND campaign_id IN ($stringv) ";
					$vcloserCampaigns 	= " AND vclog.campaign_id IN ($stringv) ";
					$call_time              = go_get_calltimes($campaign_id, $astDB);

					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL 						= "UNION select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_closer_log where 
#length_in_sec>'0' and 
week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by weekno";
						/*$TunionSQL 						= "UNION ALL select phone_number from vicidial_closer_log vcl where 
#length_in_sec>'0' and 
week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns";
						$DunionSQL 						= "UNION select status,count(*) as ccount from vicidial_closer_log vcl where 
#length_in_sec>'0' and 
week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by status";*/

						 $TunionSQL                                              = "UNION ALL select phone_number from vicidial_closer_log vcl where
#length_in_sec>'0' and
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul";
                                                $DunionSQL                                              = "UNION select status,count(*) as ccount from vicidial_closer_log where
#length_in_sec>'0' and
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul group by status";
					}
					
					// Total Calls Made
					$qtotalcallsmade 					= $astDB->rawQuery("select weekno, sum(Day0) as 'Day0', sum(Day1) as 'Day1', sum(Day2) as 'Day2', sum(Day3) as 'Day3', sum(Day4) as 'Day4', sum(Day5) as 'Day5', sum(Day6) as 'Day6' from (select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_log where 
#length_in_sec>'0' and 
campaign_id IN ($imploded_camp) and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul group by weekno $MunionSQL) t group by weekno;");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$weekno[] 					= "Week ".$row['weekno'];
							$day0[] 					= $row['Day0'];
							$day1[] 					= $row['Day1'];
							$day2[] 					= $row['Day2'];
							$day3[] 					= $row['Day3'];
							$day4[] 					= $row['Day4'];
							$day5[] 					= $row['Day5'];
							$day6[] 					= $row['Day6'];						
						}
					}
					
					$data_calls 						= array(
						"weekno" 							=> $weekno, 
						"Day0" 								=> $day0, 
						"Day1" 								=> $day1, 
						"Day2" 								=> $day2, 
						"Day3" 								=> $day3, 
						"Day4" 								=> $day4, 
						"Day5" 								=> $day5, 
						"Day6" 								=> $day6
					);
					
					/*$astDB->rawQuery("select phone_number from vicidial_log vl where 
#length_in_sec>'0' and 
campaign_id = '$campaign_id' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul $TunionSQL");*/

					$astDB->rawQuery("select phone_number from vicidial_log vl where
#length_in_sec>'0' and
campaign_id IN ($imploded_camp) and DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' ) between '$fromDate' and '$toDate' $ul $TunionSQL");
					
					$total_calls						= $astDB->getRowCount();
					
					// Total Number of Leads
					/*$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaign_id)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();*/

					$astDB->rawQuery("SELECT vl.list_id FROM  vicidial_list as vl INNER JOIN vicidial_lists as vlo ON vl.list_id = vlo.list_id WHERE
 vlo.campaign_id IN ($imploded_camp)");

                                        $total_leads                                            = $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $array_camp, "IN")
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo", "vl.list_id");
					
					$total_new							= $astDB->getRowCount();
					
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $array_camp, "IN")
						->where("date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate'")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));
					
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);
					
					// Disposition of Calls
					/*$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where 
#length_in_sec>'0' and 
week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul and campaign_id = '$campaign_id' group by status $DunionSQL) t group by status;");*/

					$qtotalstatus                                                 = $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where
#length_in_sec>'0' and
DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' ) between '$fromDate' and '$toDate' $ul and campaign_id IN ($imploded_camp) group by status $DunionSQL) t group by status;");
					$total_status						= $astDB->getRowCount();
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 						=> $status, 
						"status_name" 					=> $status_name, 
						"ccount" 						=> $ccount
					);
				}
				
				if ($request == 'monthly') {
					$stringv= go_getall_closer_campaigns($campaign_id, $astDB);
					$closerCampaigns= " AND campaign_id IN ($stringv) ";
					$vcloserCampaigns= " AND vclog.campaign_id IN ($stringv) ";
					$call_time  = go_get_calltimes($campaign_id, $astDB);

					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL 						= "UNION select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_closer_log where 
#length_in_sec>'0' and 
MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by monthname";							
						/*$TunionSQL 						= "UNION ALL select phone_number from vicidial_closer_log vcl where 
#length_in_sec>'0' and 
MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns";
						$DunionSQL 						= "UNION select status,count(*) as ccount from vicidial_closer_log vcl where 
#length_in_sec>'0' and 
MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by status";*/

						 $TunionSQL                                              = "UNION ALL select phone_number from vicidial_closer_log vcl where
#length_in_sec>'0' and
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul";
                                                $DunionSQL                                              = "UNION select status,count(*) as ccount from vicidial_closer_log where
#length_in_sec>'0' and
date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul group by status";
					}

					// Total Calls Made		
					$qtotalcallsmade					= $astDB->rawQuery("select monthname, sum(Month1) as 'Month1', sum(Month2) as 'Month2', sum(Month3) as 'Month3', sum(Month4) as 'Month4', sum(Month5) as 'Month5', sum(Month6) as 'Month6', sum(Month7) as 'Month7', sum(Month8) as 'Month8', sum(Month9) as 'Month9', sum(Month10) as 'Month10', sum(Month11) as 'Month11', sum(Month12) as 'Month12' from (select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_log where 
#length_in_sec>'0' and 
campaign_id IN ($imploded_camp) and  MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by monthname $MunionSQL) t group by monthname;");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$monthname[] 				= $row['monthname'];
							$month0[] 					= $row['Month1'];
							$month1[] 					= $row['Month2'];
							$month2[] 					= $row['Month3'];
							$month3[] 					= $row['Month4'];
							$month4[] 					= $row['Month5'];
							$month5[] 					= $row['Month6'];
							$month6[] 					= $row['Month7'];
							$month7[] 					= $row['Month8'];
							$month8[] 					= $row['Month9'];
							$month9[] 					= $row['Month10'];
							$month10[] 					= $row['Month11'];
							$month11[] 					= $row['Month12'];						
						}											
					}
					
					$data_calls 						= array(
						"monthname" 						=> $monthname, 
						"Month1" 							=> $month0, 
						"Month2" 							=> $month1, 
						"Month3" 							=> $month2, 
						"Month4" 							=> $month3, 
						"Month5" 							=> $month4, 
						"Month6" 							=> $month5, 
						"Month7" 							=> $month6, 
						"Month8" 							=> $month7, 
						"Month9" 							=> $month8, 
						"Month10" 							=> $month9, 
						"Month11" 							=> $month10, 
						"Month12" 							=> $month11
					);
					
					/*$astDB->rawQuery("select phone_number from vicidial_log vl where 
#length_in_sec>'0' and 
campaign_id = '$campaign_id' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul $TunionSQL");*/

                                        $astDB->rawQuery("select phone_number from vicidial_log vl where
#length_in_sec>'0' and
campaign_id IN ($imploded_camp) and call_date between '$fromDate' and '$toDate' $ul $TunionSQL");
					
					$total_calls						= $astDB->getRowCount();
					
					// Total Number of Leads
					/*$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaign_id)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();*/

					$astDB->rawQuery("SELECT vl.list_id FROM  vicidial_list as vl INNER JOIN vicidial_lists as vlo ON vl.list_id = vlo.list_id WHERE
 vlo.campaign_id IN ($imploded_camp)");

                                        $total_leads                                            = $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $array_camp, "IN")
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo", "vl.list_id");
					
					$total_new							= $astDB->getRowCount();
					
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $array_camp, "IN")
						//->where("MONTH(event_time)", "MONTH(event_time) between MONTH('$fromDate') and MONTH('$toDate')")
						->where("date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate'")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));
						
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}											
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);					
					
					// Disposition of Calls					
					/*$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where 
#length_in_sec>'0' and 
MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul and campaign_id = '$campaign_id' group by status $DunionSQL) t group by status;");*/

					$qtotalstatus                                           = $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where
#length_in_sec>'0' and
call_date between '$fromDate' and '$toDate' $ul and campaign_id IN ($imploded_camp) group by status $DunionSQL) t group by status;");
					$total_status						= $astDB->getRowCount();
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 							=> $status, 
						"status_name" 						=> $status_name, 
						"ccount" 							=> $ccount
					);
				}
				
				$apiresults 							= array(
					"result"								=> "success",
					"call_time" 							=> $call_time, 
					"data_calls" 							=> $data_calls, 
					"data_status" 							=> $data_status, 
					"data_agents" 							=> $data_agents, 
					"total_calls" 							=> $total_calls, 
					"total_leads" 							=> $total_leads, 
					"total_new" 							=> $total_new, 
					"total_status" 							=> $total_status
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
