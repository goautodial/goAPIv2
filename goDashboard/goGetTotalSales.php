<?php
 /**
 * @file 		goGetTotalSales.php
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
        $ul_vcl = "";
		$ul_vl = "";
    } else { 
		if($groupId !== "ADMIN"){
			$ul_vcl = "and val.user_group = '$groupId'";
			$ul_vl = "and vl.user_group = '$groupId'";
		}else{
			$ul_vcl = "";
			$ul_vl = "";
		}
    }

    $NOW = date('Y-m-d');    
    $YESTERDAY = date('Y-m-d',strtotime('-1 days'));
    
    $status = "SALE";
    $date = "call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'";
    $dateY = "call_date BETWEEN '$YESTERDAY 00:00:00' AND '$YESTERDAY 23:59:59'";
    $dateLW = "call_date BETWEEN NOW() - INTERVAL DAYOFWEEK(NOW())+6 DAY AND NOW() - INTERVAL DAYOFWEEK(NOW())-1 DAY";
   
    $query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' $ul_vcl and $date ) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' $ul_vl and $date ) as TotalSales";
    $queryY = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul_vcl) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul_vl) as TotalSalesYesterday";
    $queryLW = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul_vcl) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul_vl) as TotalSalesLastWeek";
    
    //	$query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid ) as TotalSales;";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $fresults = $astDB->rawQuery($query);
    $fresultsY =  $astDB->rawQuery($queryY);
    $fresultsLW =  $astDB->rawQuery($queryLW);
    //$fresults = mysqli_fetch_assoc($rsltv);
    //$fresultsY = mysqli_fetch_assoc($rsltvY);
    //$fresultsLW = mysqli_fetch_assoc($rsltvLW);
    $apiresults = array_merge( array( "result" => "success", "query" => $query), $fresults, $fresultsY, $fresultsLW);
?>
