<?php
    #######################################################
    #### Name: getAllDispositions.php 	               ####
    #### Description: API to get all Dispositions      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
	
	$groupId = go_get_groupid($user);
	
	if (checkIfTenant($groupId)) {
		$ul = "";
	} else {
		$stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " and campaign_id IN ($stringv) ";
		else
			$ul = "";
	}
	
	$query_date =  date('Y-m-d');
	$query = "select monthname(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0))  as 'Day7', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0))  as 'Day8', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0))  as 'Day9', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0))  as 'Day10', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0))  as 'Day11', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0))  as 'Day12', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 13, 1, 0))  as 'Day13', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 14, 1, 0))  as 'Day14', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 15, 1, 0))  as 'Day15', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 16, 1, 0))  as 'Day16', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 17, 1, 0))  as 'Day17', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 18, 1, 0))  as 'Day18', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 19, 1, 0))  as 'Day19', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 20, 1, 0))  as 'Day20', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 21, 1, 0))  as 'Day21', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 22, 1, 0))  as 'Day22', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 23, 1, 0))  as 'Day23', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 24, 1, 0))  as 'Day24', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 25, 1, 0))  as 'Day25', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 26, 1, 0))  as 'Day26', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 27, 1, 0))  as 'Day27', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 28, 1, 0))  as 'Day28', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 29, 1, 0))  as 'Day29', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 30, 1, 0))  as 'Day30', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 31, 1, 0))  as 'Day31' from vicidial_log where status='SALE' and monthname(call_date)=monthname('$query_date') $ul;";
	$rsltv = mysqli_query($link, $query);
	
		$datacount = $query->num_rows();
		$dataval   = $query->result();
		$return['datacount']=$datacount;
		$return['dataval']  =$dataval;
		
		$query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $selectSQL  ORDER BY campaign_id";
		$rsltv = mysqli_query($link,$query);
		
		while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataStat[] = $fresult['status'];			
			$dataStat[] = $fresult['status_name'];			
			$dataStat[] = $fresult['campaign_id'];
		
		$apiresults = array("result" => "success", "campaign_name" => $dataCampName, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
		}
?>

