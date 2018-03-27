<?php
 /**
 * @file 		goGetTotalInboundSales.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Warren Ipac Briones  <warren@goautodial.com>
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
		$stringv = go_getall_allowed_users($groupId, $astDB);
		if($groupId !== "ADMIN")
			$ul = " and vcl.user IN ($stringv)";
		else
			$ul = "";
    }

	$NOW = date("Y-m-d");
	$query_date =  date('Y-m-d');
	$status = "SALE";
	$date = "vcl.call_date BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";
	//select sum(calls_today) as calls_today,sum(drops_today) as drops_today,sum(answers_today) as answers_today from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'
	//$query = "select count(*) as InboundSales from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ";
	$query = "select count(*) as InboundSales
        FROM vicidial_closer_log as vcl
        LEFT JOIN vicidial_list as vl 
        ON vcl.lead_id=vl.lead_Id
        WHERE vcl.status='$status' and $date $ul";
	//$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $query ), $fresults );
?>
