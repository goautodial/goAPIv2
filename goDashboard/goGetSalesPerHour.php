<?php
 /**
 * @file 		goGetSalesPerHour.php
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

    $groupId = go_get_groupid($session_user, $astDB);

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
    $status = "SALE";

    //inbound
    $query = "SELECT date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1sales',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2sales',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3sales',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4sales',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5sales',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6sales',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7sales',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8sales',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9sales',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10sales',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11sales',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12sales',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13sales',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14sales',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15sales',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16sales',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17sales',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18sales',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19sales',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20sales',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21sales',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22sales',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23sales',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24sales' from vicidial_closer_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $ul and status='$status' group by cdate";
    $resultsinsales = $astDB->rawQuery($query);
    //$resultsinsales = mysqli_fetch_assoc($rsltv);
	
    if ($astDB->getRowCount() <= 0) {
        $resultsinsales = array();
    }



    //outbound
    $queryOut = "select date_format(call_date, '%Y-%m-%d') as cdateo,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1osales',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2osales',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3osales',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4osales',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5osales',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6osales',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7osales',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8osales',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9osales',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10osales',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11osales',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12osales',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13osales',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14osales',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15osales',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16osales',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17osales',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18osales',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19osales',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20osales',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21osales',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22osales',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23osales',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24osales' from vicidial_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $ul and status='$status' group by cdateo";
    
    $resultsoutsales = $astDB->rawQuery($queryOut);
    //$resultsoutsales = mysqli_fetch_assoc($rsltOut);
    
    if ($astDB->getRowCount() <= 0) {
        $resultsoutsales = array();
    }
    

    $apiresults = array_merge( array( "result" => "success" ), $resultsinsales, $resultsoutsales);
    //$apiresults = array( "result" => "success" , "inboundcph" => $resultsinsales, "outboundcph" => $resultsoutsales, "droppedcph" => $dresults);
?>
