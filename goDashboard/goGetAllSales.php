<?php
 /**
 * @file 		goGetAllSales.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
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

	
	$groupId = go_get_groupid($goUser, $astDB);
	
	if (checkIfTenant($groupId, $goDB)) {
		$ul = "";
	} else {
		$stringv = go_getall_allowed_campaigns($groupId, $astDB);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " and campaign_id IN ($stringv) ";
		else
			$ul = "";
	}
	
	$query_date =  date('Y-m-d');
	$query = "select monthname(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0))  as 'Day7', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0))  as 'Day8', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0))  as 'Day9', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0))  as 'Day10', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0))  as 'Day11', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0))  as 'Day12', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 13, 1, 0))  as 'Day13', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 14, 1, 0))  as 'Day14', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 15, 1, 0))  as 'Day15', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 16, 1, 0))  as 'Day16', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 17, 1, 0))  as 'Day17', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 18, 1, 0))  as 'Day18', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 19, 1, 0))  as 'Day19', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 20, 1, 0))  as 'Day20', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 21, 1, 0))  as 'Day21', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 22, 1, 0))  as 'Day22', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 23, 1, 0))  as 'Day23', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 24, 1, 0))  as 'Day24', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 25, 1, 0))  as 'Day25', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 26, 1, 0))  as 'Day26', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 27, 1, 0))  as 'Day27', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 28, 1, 0))  as 'Day28', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 29, 1, 0))  as 'Day29', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 30, 1, 0))  as 'Day30', sum(if(DAYOFMONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 31, 1, 0))  as 'Day31' from vicidial_log where status='SALE' and monthname(call_date)=monthname('$query_date') $ul;";
	$rsltv = $astDB->rawQuery($query);
	
	$datacount = $astDB->getRowCount();
	$dataval   = $rsltv;
	$return['datacount']=$datacount;
	$return['dataval']  =$dataval;
	
	//$query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $selectSQL  ORDER BY campaign_id";
	$astDB->orderBy('campaign_id', 'desc');
	$rsltv = $astDB->get('vicidial_campaign_statuses', null, 'status,status_name,campaign_id');
	
	foreach ($rsltv as $fresult){
		$dataStat[] = $fresult['status'];			
		$dataStat[] = $fresult['status_name'];			
		$dataStat[] = $fresult['campaign_id'];
	
		$apiresults = array("result" => "success", "campaign_name" => $dataCampName, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
	}
?>

