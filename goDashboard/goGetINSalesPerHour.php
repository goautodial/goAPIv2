<?php
 /**
 * @file 		goGetINSalesPerHour.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jericho James Milo  <james@goautodial.com>
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
        $stringv = go_getall_allowed_campaigns($groupId, $astDB);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = "and vcl.campaign_id IN ($stringv)";
		else
			$ul = "";
    }

    $NOW = date("Y-m-d");
	$query_date =  date('Y-m-d H');
	$status = "SALE";
	$date = "vcl.call_date BETWEEN '$query_date:00:00' AND '$query_date:59:59'";
    $query = "select count(*) as getINSalesPerHour
			FROM vicidial_closer_log as vcl
			LEFT JOIN vicidial_list as vl 
			ON vcl.lead_id=vl.lead_Id
			WHERE vcl.status='$status' $ul and $date ";
	//$query = "select concat(round((select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ),2),'%')/8 as getInSalesPerHour;";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $query), $fresults );
?>
