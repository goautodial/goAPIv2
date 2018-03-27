<?php
 /**
 * @file 		goGetTotalOutboundSales.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
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

    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
		if($groupId !== "ADMIN")
			$ul = " and vlog.user_group = '$groupId'";
		else
			$ul = "";
    }

	$NOW = date("Y-m-d");
	$query_date =  date('Y-m-d');
	$status = "SALE";
	$date = "vlog.call_date BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";
//select sum(calls_today) as calls_today,sum(drops_today) as drops_today,sum(answers_today) as answers_today from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'
   //$query = "select count(*) as OutboundSales from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid";
    $query = "select count(*) as OutboundSales
            FROM vicidial_log as vlog
            LEFT JOIN vicidial_list as vl 
            ON vlog.lead_id=vl.lead_Id
            WHERE vlog.status='$status' $ul and $date ";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $rsltv = $astDB->rawQuery($query);
	$count = $astDB->getRowCount();
	if($count > 0){
		$fresults = $rsltv[0];
		$result = $fresults['OutboundSales'];
	}else{
		$result = 0;
	}
	
	
    $apiresults = array( "result" => "success", "OutboundSales" => $result, "query" => $query);
?>
